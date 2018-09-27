<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Api\ClientInterface;
use Vertex\Tax\Exception\ApiRequestException;
use Vertex\Tax\Exception\ApiRequestException\ConnectionFailureException;
use Vertex\Tax\Model\ApiClient\SoapFaultConverterInterface;

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

    /** @var SoapFaultConverterInterface */
    private $soapFaultConverter;

    private $legacyCiphers = [
        'DHE-RSA-AES256-SHA',
        'DHE-DSS-AES256-SHA',
        'AES256-SHA',
        'KRB5-DES-CBC3-MD5',
        'KRB5-DES-CBC3-SHA',
        'EDH-RSA-DES-CBC3-SHA',
        'EDH-DSS-DES-CBC3-SHA',
        'DES-CBC3-SHA',
        'DES-CBC3-MD5',
        'DHE-RSA-AES128-SHA',
        'DHE-DSS-AES128-SHA',
        'AES128-SHA',
        'RC2-CBC-MD5',
        'KRB5-RC4-MD5',
        'KRB5-RC4-SHA',
        'RC4-SHA',
        'RC4-MD5',
        'RC4-MD5',
        'KRB5-DES-CBC-MD5',
        'KRB5-DES-CBC-SHA',
        'EDH-RSA-DES-CBC-SHA',
        'EDH-DSS-DES-CBC-SHA',
        'DES-CBC-SHA',
        'DES-CBC-MD5',
        'EXP-KRB5-RC2-CBC-MD5',
        'EXP-KRB5-DES-CBC-MD5',
        'EXP-KRB5-RC2-CBC-SHA',
        'EXP-KRB5-DES-CBC-SHA',
        'EXP-EDH-RSA-DES-CBC-SHA',
        'EXP-EDH-DSS-DES-CBC-SHA',
        'EXP-DES-CBC-SHA',
        'EXP-RC2-CBC-MD5',
        'EXP-RC2-CBC-MD5',
        'EXP-KRB5-RC4-MD5',
        'EXP-KRB5-RC4-SHA',
        'EXP-RC4-MD5',
        'EXP-RC4-MD5',
    ];

    /**
     * @param LoggerInterface $logger
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param SoapClientFactory $soapClientFactory
     * @param RequestLogger $requestLogger
     * @param ObjectConverter $objectConverter
     * @param SoapFaultConverterInterface $soapFaultConverter
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        StoreManagerInterface $storeManager,
        SoapClientFactory $soapClientFactory,
        RequestLogger $requestLogger,
        ObjectConverter $objectConverter,
        SoapFaultConverterInterface $soapFaultConverter
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->soapClientFactory = $soapClientFactory;
        $this->requestLogger = $requestLogger;
        $this->objectConverter = $objectConverter;
        $this->soapFaultConverter = $soapFaultConverter;
    }

    /**
     * {@inheritdoc}
     * @deprecated
     */
    public function sendApiRequest(array $request, $type, OrderInterface $order = null)
    {
        try {
            return $this->performRequest($request, $type, ScopeInterface::SCOPE_STORE, $this->getStoreId($order));
        } catch (ApiRequestException $e) {
            return false;
        }
    }

    /**
     * Perform an API Request against Vertex
     *
     * @param array $request A properly formatted SOAP request object.  Please consult Vertex API documentation for
     *   more information
     * @param string $type One of tax_area_lookup, invoice, invoice_refund or quote
     * @param string|null $scopeType
     * @param string|null $scopeCode
     * @return array A SOAP formatted response
     * @throws ConnectionFailureException when the endpoint cannot be reached
     * @throws ApiRequestException when we're unsure what exactly went wrong
     */
    public function performRequest(array $request, $type, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $apiUrl = $this->config->getVertexHost($scopeCode, $scopeType);
        if ($type === 'tax_area_lookup') {
            $apiUrl = $this->config->getVertexAddressHost($scopeCode, $scopeType);
        }

        $apiUrl = $this->getWsdlUrl($apiUrl);

        $client = null;
        try {
            $client = $this->createSoapClient($apiUrl);
            $taxResponse = $this->performSoapCall($client, $type, $request, $scopeType, $scopeCode);
            $taxResponseArray = $this->objectConverter->convertToArray($taxResponse);
        } catch (\SoapFault $e) {
            $this->logException($e, $type, $client);
            $newException = $this->createExceptionFromSoapFault($e);
            throw $newException;
        } catch (\Exception $e) {
            $this->logException($e, $type, $client);
            throw new ApiRequestException(__('An unknown error occurred performing the request'), $e);
        }

        $keys = is_array($taxResponseArray) ? array_keys($taxResponseArray) : [];
        $taxResponseArray = isset($keys[1]) ? $taxResponseArray[$keys[1]] : [];

        $totalTax = $this->getTotalTax($taxResponseArray);
        $this->logRequest(
            $type,
            $client->__getLastRequest(),
            $client->__getLastResponse(),
            isset($totalTax) ? $totalTax : null
        );
        return $taxResponseArray;
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
            'stream_context' => $this->createStreamContext(),
        ];

        $soapParams['stream_context'] = $context; // for TLS 1.2

        return $this->soapClientFactory->create($apiUrl, $soapParams);
    }

    /**
     * Create a communication context for the client.
     *
     * Returns a context based on PHP version to properly negotiate on TLS 1.2.
     *
     * @return resource
     */
    private function createStreamContext()
    {
        if (version_compare(phpversion(), '5.6') < 0) {
            return stream_context_create(
                [
                    'ssl' => [
                        'ciphers' => implode(':', $this->legacyCiphers),
                    ]
                ]
            );
        }

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
     * Log an API Request
     *
     * @param string $type
     * @param string $requestXml
     * @param string $responseXml
     * @param int|array $totalTax
     * @param int $taxAreaId
     */
    private function logRequest($type, $requestXml, $responseXml, $totalTax = null, $taxAreaId = null)
    {
        try {
            $this->requestLogger->log(
                $type,
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
     * @param string $scopeType
     * @param string $scopeCode
     * @return mixed
     * @throws LocalizedException
     * @throws \SoapFault
     */
    private function performSoapCall(
        \SoapClient $client,
        $type,
        $request,
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        if ($type === 'tax_area_lookup') {
            $lookupFunc = $this->config->getValidationFunction($scopeCode, $scopeType);
            if (!$lookupFunc) {
                throw new LocalizedException(__('No Validation function set'));
            }
            $taxResponse = $client->$lookupFunc($request);
        } elseif (in_array($type, ['quote', 'invoice', 'invoice_refund'])) {
            $calculationFunc = $this->config->getCalculationFunction($scopeCode, $scopeType);
            if (!$calculationFunc) {
                throw new LocalizedException(__('No Calculation function set'));
            }
            $taxResponse = $client->$calculationFunc($request);
        } else {
            throw new \InvalidArgumentException('Invalid request type');
        }
        if ($taxResponse instanceof \SoapFault) {
            throw $taxResponse;
        }

        return $taxResponse;
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
     * Convert a SoapFault to some form of {@see ApiRequestException}
     *
     * @param \SoapFault $fault
     * @return ApiRequestException|ConnectionFailureException
     */
    private function createExceptionFromSoapFault(\SoapFault $fault)
    {
        $result = $this->soapFaultConverter->convert($fault);
        return $result ?: new ApiRequestException(__('An unknown error occurred performing the request'), $fault);
    }
}
