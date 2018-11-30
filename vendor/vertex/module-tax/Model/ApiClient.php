<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Api\ClientInterface;
use Vertex\Tax\Model\ApiClient\ObjectConverter;
use Vertex\Utility\SoapClientFactory;

/**
 * {@inheritdoc}
 * @deprecated
 */
class ApiClient implements ClientInterface
{
    const CONNECTION_TIMEOUT = 12; // seconds

    /** @var Config */
    private $config;

    /** @var LoggerInterface */
    private $logger;

    /** @var ObjectConverter */
    private $objectConverter;

    /** @var RequestLogger */
    private $requestLogger;

    /** @var SoapClientFactory */
    private $soapClientFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

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
     * {@inheritdoc}
     * @deprecated
     */
    public function sendApiRequest(array $request, $type, OrderInterface $order = null)
    {
        $scopeType = ScopeInterface::SCOPE_STORE;
        $scopeCode = $this->getStoreId($order);

        $apiUrl = $this->config->getVertexHost($scopeCode, $scopeType);
        if ($type === 'tax_area_lookup') {
            $apiUrl = $this->config->getVertexAddressHost($scopeCode, $scopeType);
        }

        $apiUrl = $this->getWsdlUrl($apiUrl);

        $client = null;
        try {
            $client = $this->createSoapClient($apiUrl);
            $taxResponse = $this->performSoapCall($client, $type, $request);
            $taxResponseArray = $this->objectConverter->convertToArray($taxResponse);
        } catch (\Exception $e) {
            $this->logException($e, $type, $client);
            return false;
        }

        $this->logRequest(
            $type,
            $client->__getLastRequest(),
            $client->__getLastResponse()
        );
        return $taxResponseArray;
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
            'stream_context' => $this->createStreamContext(),
        ];

        $soapParams['stream_context'] = $context; // for TLS 1.2

        return $this->soapClientFactory->create($apiUrl, $soapParams);
    }

    /**
     * Create a communication context for the client.
     *
     * Returns context to properly negotiate on TLS 1.2.
     *
     * @return resource
     */
    private function createStreamContext()
    {
        return stream_context_create(
            [
                'ssl' => [
                    'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                    'ciphers' => 'SHA2',
                ]
            ]
        );
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
     * Log an exception that occurred during an API request
     *
     * @param \Throwable $exception
     * @param string $type Request Type
     * @param \SoapClient|null $client
     */
    private function logException($exception, $type, \SoapClient $client = null)
    {
        $this->logger->critical($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        $this->logRequest(
            $type,
            $client !== null ? $client->__getLastRequest() : '',
            $client !== null ? $client->__getLastResponse() : ''
        );
    }

    /**
     * Log an API Request
     *
     * @param string $type
     * @param string $requestXml
     * @param string $responseXml
     */
    private function logRequest($type, $requestXml, $responseXml)
    {
        try {
            $this->requestLogger->log(
                $type,
                $requestXml,
                $responseXml
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
     * @param string $scopeType
     * @param string $scopeCode
     * @return mixed
     * @throws Exception
     * @throws \SoapFault
     */
    private function performSoapCall(
        \SoapClient $client,
        $type,
        $request
    ) {
        if ($type === 'tax_area_lookup') {
            $taxResponse = $client->LookupTaxAreas60($request);
        } elseif (in_array($type, ['quote', 'invoice', 'invoice_refund'])) {
            $taxResponse = $client->CalculateTax60($request);
        } else {
            throw new \Exception('Invalid request type');
        }
        if ($taxResponse instanceof \SoapFault) {
            throw $taxResponse;
        }

        return $taxResponse;
    }
}
