<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Temando\Shipping\Rest\Adapter\OrderApiInterface;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Exception\RestClientErrorException;
use Temando\Shipping\Rest\Request\OrderRequestInterface;
use Temando\Shipping\Rest\Request\RequestHeadersInterface;
use Temando\Shipping\Rest\Response\AllocateOrder;
use Temando\Shipping\Rest\Response\AllocateOrderInterface;
use Temando\Shipping\Rest\Response\CreateOrder;
use Temando\Shipping\Rest\Response\CreateOrderInterface;
use Temando\Shipping\Rest\Response\Errors;
use Temando\Shipping\Rest\Response\GetCollectionPoints;
use Temando\Shipping\Rest\Response\GetCollectionPointsInterface;
use Temando\Shipping\Rest\Response\UpdateOrder;
use Temando\Shipping\Rest\Response\UpdateOrderInterface;
use Temando\Shipping\Rest\SchemaMapper\ParserInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando REST API Order Operations Adapter
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderAdapter implements OrderApiInterface
{
    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $bearerToken;

    /**
     * @var RequestHeadersInterface
     */
    private $requestHeaders;

    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * @var RestClientInterface
     */
    private $restClient;

    /**
     * @var ParserInterface
     */
    private $responseParser;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderAdapter constructor.
     * @param WsConfigInterface $config
     * @param RequestHeadersInterface $requestHeaders
     * @param AuthenticationInterface $auth,
     * @param RestClientInterface $restClient
     * @param ParserInterface $responseParser
     * @param LoggerInterface $logger
     */
    public function __construct(
        WsConfigInterface $config,
        RequestHeadersInterface $requestHeaders,
        AuthenticationInterface $auth,
        RestClientInterface $restClient,
        ParserInterface $responseParser,
        LoggerInterface $logger
    ) {
        $this->endpoint = $config->getApiEndpoint();
        $this->accountId = $config->getAccountId();
        $this->bearerToken = $config->getBearerToken();

        $this->requestHeaders = $requestHeaders;
        $this->auth = $auth;
        $this->restClient = $restClient;
        $this->responseParser = $responseParser;
        $this->logger = $logger;
    }

    /**
     * Create order at the platform and retrieve applicable shipping options.
     *
     * For quoting only (if the order is not yet complete/placed) set additional request parameter `persist=false`.
     *
     * @param OrderRequestInterface $request
     * @return CreateOrderInterface
     * @throws AdapterException
     */
    public function createOrder(OrderRequestInterface $request)
    {
        $requestParams = $request->getRequestParams(self::ACTION_CREATE);
        $uri = sprintf('%s/orders?%s', $this->endpoint, http_build_query($requestParams));
        $requestBody = $request->getRequestBody();

        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->post($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var CreateOrder $response */
            $response = $this->responseParser->parse($rawResponse, CreateOrder::class);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $response;
    }

    /**
     * Create order at the platform and retrieve applicable collection points.
     *
     * @param OrderRequestInterface $request
     * @return GetCollectionPointsInterface
     * @throws AdapterException
     */
    public function getCollectionPoints(OrderRequestInterface $request)
    {
        $requestParams = $request->getRequestParams(self::ACTION_GET_COLLECTION_POINTS);
        $uri = sprintf('%s/orders?%s', $this->endpoint, http_build_query($requestParams));
        $requestBody = $request->getRequestBody();

        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->post($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetCollectionPoints $response */
            $response = $this->responseParser->parse($rawResponse, GetCollectionPoints::class);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $response;
    }

    /**
     * Manifest order and retrieve allocated shipments.
     *
     * @param OrderRequestInterface $request
     * @return AllocateOrderInterface
     * @throws AdapterException
     */
    public function allocateOrder(OrderRequestInterface $request)
    {
        $requestParams = $request->getRequestParams(self::ACTION_ALLOCATE);
        $uri = sprintf('%s/orders?%s', $this->endpoint, http_build_query($requestParams));
        $requestBody = $request->getRequestBody();

        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->post($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var AllocateOrder $response */
            $response = $this->responseParser->parse($rawResponse, AllocateOrder::class);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $response;
    }

    /**
     * Update order.
     *
     * @param OrderRequestInterface $request
     * @return UpdateOrderInterface
     * @throws AdapterException
     */
    public function updateOrder(OrderRequestInterface $request)
    {
        $requestParams = $request->getRequestParams(self::ACTION_UPDATE);
        $queryParams = http_build_query($requestParams);
        $uri = sprintf('%s/orders/%s', $this->endpoint, ...$request->getPathParams());
        $uri = "$uri?$queryParams";
        $requestBody = $request->getRequestBody();

        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse =  $this->restClient->put($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var UpdateOrder $response */
            $response = $this->responseParser->parse($rawResponse, UpdateOrder::class);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $response;
    }
}
