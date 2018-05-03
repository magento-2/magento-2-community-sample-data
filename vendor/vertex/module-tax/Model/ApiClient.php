<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Api\ClientInterface;

/**
 * Initial implementation of {@see ClientInterface}
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApiClient implements ClientInterface
{
    const CONNECTION_TIMEOUT = 12; // seconds

    /** @var LoggerInterface */
    private $logger;

    /** @var Config */
    private $config;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var SoapClientFactory */
    private $soapClientFactory;

    /** @var RequestLogger */
    private $requestLogger;

    /** @var ObjectConverter */
    private $objectConverter;

    /**
     * @param LoggerInterface $logger
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param SoapClientFactory $soapClientFactory
     * @param RequestLogger $requestLogger
     * @param ObjectConverter $objectConverter
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        StoreManagerInterface $storeManager,
        SoapClientFactory $soapClientFactory,
        RequestLogger $requestLogger,
        ObjectConverter $objectConverter
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->soapClientFactory = $soapClientFactory;
        $this->requestLogger = $requestLogger;
        $this->objectConverter = $objectConverter;
    }

    /**
     * @inheritdoc
     */
    public function sendApiRequest(array $request, $type, OrderInterface $order = null)
    {
        $objectId = $this->getOrderId($order);
        $storeId = $this->getStoreId($order);

        $apiUrl = $this->config->getVertexHost($storeId);
        if ($type === 'tax_area_lookup') {
            $apiUrl = $this->config->getVertexAddressHost($storeId);
        }

        $apiUrl = $this->getWsdlUrl($apiUrl);

        try {
            $client = $this->createSoapClient($apiUrl);
        } catch (\Exception $e) {
            $this->logger->critical(
                $e->getMessage() . PHP_EOL . $e->getTraceAsString()
            );
            return false;
        }

        try {
            $taxResponse = $this->performSoapCall($client, $type, $request, $storeId);
            $taxResponseArray = $this->objectConverter->convertToArray($taxResponse);

            $keys = is_array($taxResponseArray) ? array_keys($taxResponseArray) : [];
            $taxResponseArray = isset($keys[1]) ? $taxResponseArray[$keys[1]] : [];

            $totalTax = $this->getTotalTax($taxResponseArray);
            $this->logRequest(
                $type,
                $objectId,
                $client->__getLastRequest(),
                $client->__getLastResponse(),
                isset($totalTax) ? $totalTax : null
            );
            return $taxResponseArray;
        } catch (\SoapFault $e) {
            $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            $this->logRequest(
                $type,
                $objectId,
                $client->__getLastRequest(),
                $client->__getLastResponse(),
                isset($totalTax) ? $totalTax : null
            );
            return false;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Get the WSDL version of an API URL
     *
     * @param string $apiUrl
     * @return string
     */
    private function getWsdlUrl($apiUrl)
    {
        if (stripos($apiUrl, 'wsdl') === false) {
            $apiUrl .= '?wsdl';
        }
        return $apiUrl;
    }

    /**
     * Create a SOAP client for use with the API
     *
     * Enforces TLSv1.2 with SHA2 on a SOAP 1.1 Call
     *
     * @param $apiUrl
     * @return \SoapClient
     */
    private function createSoapClient($apiUrl)
    {
        $soapParams = [
            'trace' => true,
            'soap_version' => SOAP_1_1
        ];

        $context = [
            'ssl_method' => SOAP_SSL_METHOD_TLS,
            'connection_timeout' => static::CONNECTION_TIMEOUT,
            // stream_context_create necessary to enforce TLS 1.2
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                        'ciphers' => 'SHA2',
                    ]
                ]
            )
        ];

        $soapParams['stream_context'] = $context; // for TLS 1.2

        return $this->soapClientFactory->create($apiUrl, $soapParams);
    }

    /**
     * Log an API Request
     *
     * @param string $type
     * @param string|int $objectId
     * @param string $requestXml
     * @param string $responseXml
     * @param int|array $totalTax
     * @param int $taxAreaId
     */
    private function logRequest($type, $objectId, $requestXml, $responseXml, $totalTax = null, $taxAreaId = null)
    {
        try {
            $this->requestLogger->log(
                $type,
                $objectId,
                $requestXml,
                $responseXml,
                $totalTax,
                $taxAreaId
            );
        } catch (\Exception $originalException) {
            // Logging Exception
            $exception = new \Exception('Failed to log Vertex Request', 0, $originalException);
            $this->logger->critical(
                $exception->getMessage() . PHP_EOL . $exception->getTraceAsString()
            );
        }
    }

    /**
     * Perform a SOAP Call given a client and request data and type
     *
     * Uses type to determine which function to call against SOAP
     *
     * @param \SoapClient $client
     * @param string $type
     * @param array $request
     * @param int|string|null $storeId
     * @return mixed
     * @throws LocalizedException
     * @throws \SoapFault
     */
    private function performSoapCall(\SoapClient $client, $type, $request, $storeId = null)
    {
        if ($type === 'tax_area_lookup') {
            $lookupFunc = $this->config->getValidationFunction($storeId);
            if (!$lookupFunc) {
                throw new LocalizedException(__('No Validation function set'));
            }
            $taxResponse = $client->$lookupFunc($request);
        } else {
            $calculationFunc = $this->config->getCalculationFunction($storeId);
            if (!$calculationFunc) {
                throw new LocalizedException(__('No Calculation function set'));
            }
            $taxResponse = $client->$calculationFunc($request);
        }
        if ($taxResponse instanceof \SoapFault) {
            throw $taxResponse;
        }

        return $taxResponse;
    }

    /**
     * Retrieve the ID of an Order object
     *
     * @param OrderInterface|null $order
     * @return int|null
     */
    private function getOrderId(OrderInterface $order = null)
    {
        return $order !== null ? $order->getEntityId() : null;
    }

    /**
     * Retrieve the Store ID relevant to the request
     *
     * @param OrderInterface|null $order
     * @return int
     */
    private function getStoreId(OrderInterface $order = null)
    {
        return $order !== null ? $order->getStoreId() : $this->storeManager->getStore()->getId();
    }

    /**
     * Retrieve the total tax calculated from a response
     *
     * @param array $taxResponseArray
     * @return float
     */
    private function getTotalTax($taxResponseArray)
    {
        $totalTax = 0;

        if (isset($taxResponseArray['TotalTax'])) {
            $totalTax = $taxResponseArray['TotalTax'];
        }

        return $totalTax;
    }
}
