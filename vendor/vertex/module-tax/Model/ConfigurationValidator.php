<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Vertex\Tax\Exception\ApiRequestException;
use Vertex\Tax\Exception\ApiRequestException\ConnectionFailureException;
use Vertex\Tax\Model\ConfigurationValidator\ValidSampleRequestFactory;
use Vertex\Tax\Model\ConfigurationValidator\Result;
use Vertex\Tax\Model\ConfigurationValidator\ResultFactory;
use Vertex\Tax\Model\TaxArea\TaxAreaRequestFactory;

/**
 * Validates the Credentials provided in the configuration
 */
class ConfigurationValidator
{
    /** @var Config */
    private $config;

    /** @var Request\Address */
    private $addressFormatter;

    /** @var ApiClient */
    private $apiClient;

    /** @var TaxAreaRequestFactory */
    private $taxAreaRequestFactory;

    /** @var ValidSampleRequestFactory */
    private $sampleRequestFactory;

    /** @var ResultFactory */
    private $resultFactory;

    /**
     * @param Config $config
     * @param ApiClient $apiClient
     * @param Request\Address $addressFormatter
     * @param TaxAreaRequestFactory $taxAreaRequestFactory
     * @param ValidSampleRequestFactory $sampleRequestFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Config $config,
        ApiClient $apiClient,
        Request\Address $addressFormatter,
        TaxAreaRequestFactory $taxAreaRequestFactory,
        ValidSampleRequestFactory $sampleRequestFactory,
        ResultFactory $resultFactory
    ) {
        $this->config = $config;
        $this->apiClient = $apiClient;
        $this->addressFormatter = $addressFormatter;
        $this->taxAreaRequestFactory = $taxAreaRequestFactory;
        $this->sampleRequestFactory = $sampleRequestFactory;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Validate configuration
     *
     * @param string $scopeType
     * @param string|int $scopeId
     * @return Result
     */
    public function execute($scopeType, $scopeId)
    {
        /** @var Result $result */
        $result = $this->resultFactory->create();

        $this->validateConfigurationComplete($result, $scopeType, $scopeId);
        if (!$result->isValid()) {
            return $result;
        }

        $this->validateAddressComplete($result, $scopeType, $scopeId);
        if (!$result->isValid()) {
            return $result;
        }

        $this->validateAddressLookup($result, $scopeType, $scopeId);

        if ($result->isValid()) {
            $this->validateCalculationService($result, $scopeType, $scopeId);
        }

        return $result;
    }

    /**
     * Validates that Vertex API, Lookup API, and Trusted ID have been configured in the admin
     *
     * @param Result $result
     * @param string $scopeType
     * @param string|int $scopeId
     * @return Result
     */
    private function validateConfigurationComplete(Result $result, $scopeType, $scopeId)
    {
        $missing = [];
        if (!$this->config->getVertexHost($scopeId, $scopeType)) {
            $missing[] = 'Vertex API URL';
        }
        if (!$this->config->getVertexAddressHost($scopeId, $scopeType)) {
            $missing[] = 'Address Lookup API URL';
        }

        if (!$this->config->getTrustedId($scopeId, $scopeType)) {
            $missing[] = 'Trusted ID';
        }

        if (!empty($missing)) {
            $result->setMessage('Configuration Incomplete, Missing: %1');
            $result->setArguments([implode(', ', $missing)]);
            $result->setValid(false);
        } else {
            $result->setValid(true);
        }

        return $result;
    }

    /**
     * Validates that the Company Address has been configured in the admin
     *
     * @param Result $result
     * @param string $scopeType
     * @param string|int $scopeId
     * @return Result
     */
    private function validateAddressComplete(Result $result, $scopeType, $scopeId)
    {
        if (!$this->config->getCompanyRegionId($scopeId, $scopeType)) {
            $missing[] = 'Company State';
        }

        if (!$this->config->getCompanyCountry($scopeId, $scopeType)) {
            $missing[] = 'Company Country';
        }

        if (!$this->config->getCompanyStreet1($scopeId, $scopeType)) {
            $missing[] = 'Company Street';
        }

        if (!$this->config->getCompanyCity($scopeId, $scopeType) ||
            !$this->config->getCompanyPostalCode($scopeId, $scopeType)
        ) {
            $missing[] = 'one of Company City or Postcode';
        }

        if (!empty($missing)) {
            $result->setMessage('Address Incomplete, Missing: %1');
            $result->setArguments([implode(', ', $missing)]);
            $result->setValid(false);
        } else {
            $result->setValid(true);
        }

        return $result;
    }

    /**
     * Validates the Company Address against the Lookup API
     *
     * @param Result $result
     * @param string $scopeType
     * @param string|int $scopeId
     * @return Result
     */
    private function validateAddressLookup(Result $result, $scopeType, $scopeId)
    {
        try {
            $address = $this->addressFormatter->getFormattedAddressData(
                [
                    $this->config->getCompanyStreet1($scopeId, $scopeType),
                    $this->config->getCompanyStreet2($scopeId, $scopeType)
                ],
                $this->config->getCompanyCity($scopeId, $scopeType),
                $this->config->getCompanyRegionId($scopeId, $scopeType),
                $this->config->getCompanyPostalCode($scopeId, $scopeType),
                $this->config->getCompanyCountry($scopeId, $scopeType)
            );
        } catch (NoSuchEntityException $exception) {
            $result->setMessage('Invalid Address');
            $result->setValid(false);
            return $result;
        }

        if ($address['Country'] !== 'USA') {
            $result->setValid(true);
            return $result;
        }

        $result->setValid(false);
        try {
            $this->taxAreaRequestFactory->create()->taxAreaLookup(
                $address,
                $scopeId,
                $scopeType
            );
            $result->setValid(true);
        } catch (ConnectionFailureException $e) {
            $result->setMessage('Unable to connect to Address Validation API');
        } catch (ApiRequestException $e) {
            $result->setMessage('Unable to validate address against API');
        }

        return $result;
    }

    /**
     * Verify Vertex API connectivity by performing a live tax calculation request.
     *
     * @param Result $result
     * @param string $scopeType
     * @param string $scopeId
     * @return Result
     */
    private function validateCalculationService(Result $result, $scopeType, $scopeId)
    {
        $request = $this->sampleRequestFactory->create();
        if ($this->apiClient instanceof ApiClient) {
            try {
                $response = $this->apiClient->performRequest($request, 'quote', $scopeType, $scopeId);
            } catch (\Exception $e) {
                $response = false;
            }
        } else {
            $response = $this->apiClient->sendApiRequest($request, 'quote');
        }

        if ($response === false) {
            $result->setMessage('Unable to connect to Calculation API');
            $result->setValid(false);
        } else {
            $result->setValid(true);
        }

        return $result;
    }
}
