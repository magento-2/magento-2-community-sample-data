<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Vertex\Tax\Model\CredentialChecker\Result;
use Vertex\Tax\Model\CredentialChecker\ResultFactory;
use Vertex\Tax\Model\TaxArea\TaxAreaRequestFactory;

/**
 * Validates the Credentials provided in the configuration
 */
class CredentialChecker
{
    /** @var Config */
    private $config;

    /** @var Request\Address */
    private $addressFormatter;

    /** @var TaxAreaRequestFactory */
    private $taxAreaRequestFactory;

    /** @var ResultFactory */
    private $resultFactory;

    /**
     * @param Config $config
     * @param Request\Address $addressFormatter
     * @param TaxAreaRequestFactory $taxAreaRequestFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Config $config,
        Request\Address $addressFormatter,
        TaxAreaRequestFactory $taxAreaRequestFactory,
        ResultFactory $resultFactory
    ) {
        $this->config = $config;
        $this->addressFormatter = $addressFormatter;
        $this->taxAreaRequestFactory = $taxAreaRequestFactory;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Validate configuration
     *
     * @param string|int $scopeId
     * @return Result
     */
    public function validate($scopeId)
    {
        /** @var Result $result */
        $result = $this->resultFactory->create();

        $this->validateConfigurationComplete($result, $scopeId);
        if (!$result->isValid()) {
            return $result;
        }

        $this->validateAddressComplete($result, $scopeId);
        if (!$result->isValid()) {
            return $result;
        }

        $this->validateAddressLookup($result, $scopeId);
        return $result;
    }

    /**
     * Validates that Vertex API, Lookup API, and Trusted ID have been configured in the admin
     *
     * @param Result $result
     * @param string|int $scopeId
     * @return Result
     */
    private function validateConfigurationComplete(Result $result, $scopeId)
    {
        $missing = [];
        if (!$this->config->getVertexHost($scopeId)) {
            $missing[] = 'Vertex API URL';
        }
        if (!$this->config->getVertexAddressHost($scopeId)) {
            $missing[] = 'Address Lookup API URL';
        }

        if (!$this->config->getTrustedId($scopeId)) {
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
     * @param string|int $scopeId
     * @return Result
     */
    private function validateAddressComplete(Result $result, $scopeId)
    {
        if (!$this->config->getCompanyRegionId($scopeId)) {
            $missing[] = 'Company State';
        }

        if (!$this->config->getCompanyCountry($scopeId)) {
            $missing[] = 'Company Country';
        }

        if (!$this->config->getCompanyStreet1($scopeId)) {
            $missing[] = 'Company Street';
        }

        if (!$this->config->getCompanyCity($scopeId) ||
            !$this->config->getCompanyPostalCode($scopeId)
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
     * @param string|int $scopeId
     * @return Result
     */
    private function validateAddressLookup(Result $result, $scopeId)
    {
        try {
            $address = $this->addressFormatter->getFormattedAddressData(
                [
                    $this->config->getCompanyStreet1($scopeId),
                    $this->config->getCompanyStreet2($scopeId)
                ],
                $this->config->getCompanyCity($scopeId),
                $this->config->getCompanyRegionId($scopeId),
                $this->config->getCompanyPostalCode($scopeId),
                $this->config->getCompanyCountry($scopeId)
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

        $response = $this->taxAreaRequestFactory->create()->taxAreaLookup(
            $address
        );

        if ($response === false) {
            $result->setMessage('Unable to validate address against API');
            $result->setValid(false);
            return $result;
        }

        $result->setValid(true);
        return $result;
    }
}
