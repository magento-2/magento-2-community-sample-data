<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Temando\Shipping\Rest\Adapter\FulfillmentApiInterface;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Exception\RestClientErrorException;
use Temando\Shipping\Rest\Request\FulfillmentRequestInterface;
use Temando\Shipping\Rest\Request\ItemRequestInterface;
use Temando\Shipping\Rest\Request\ListRequestInterface;
use Temando\Shipping\Rest\Request\RequestHeadersInterface;
use Temando\Shipping\Rest\Response\DataObject\Fulfillment;
use Temando\Shipping\Rest\Response\Document\Errors;
use Temando\Shipping\Rest\Response\Document\GetFulfillment;
use Temando\Shipping\Rest\Response\Document\GetFulfillments;
use Temando\Shipping\Rest\SchemaMapper\ParserInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando REST API Fulfillment Operations Adapter
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class FulfillmentAdapter implements FulfillmentApiInterface
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
     * @param ItemRequestInterface $request
     *
     * @return Fulfillment
     * @throws AdapterException
     */
    public function getFulfillment(ItemRequestInterface $request)
    {
        $uri = sprintf('%s/fulfillments/%s', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, [], $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetFulfillment $response */
            $response = $this->responseParser->parse($rawResponse, GetFulfillment::class);
            $fulfillment = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $fulfillment;
    }

    /**
     * @param ListRequestInterface $request
     *
     * @return Fulfillment[]
     * @throws AdapterException
     */
    public function getFulfillments(ListRequestInterface $request)
    {
        $uri = sprintf('%s/fulfillments', $this->endpoint);
        $queryParams = $request->getRequestParams();

        $this->logger->log(LogLevel::DEBUG, sprintf('%s?%s', $uri, http_build_query($queryParams)));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, $queryParams, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetFulfillments $response */
            $response = $this->responseParser->parse($rawResponse, GetFulfillments::class);
            $fulfillments = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $fulfillments = [];
        }

        return $fulfillments;
    }

    /**
     * Create fulfillment at the platform.
     *
     * @param FulfillmentRequestInterface $request
     *
     * @return Fulfillment
     * @throws AdapterException
     */
    public function createFulfillment(FulfillmentRequestInterface $request)
    {
        $uri = sprintf('%s/fulfillments', $this->endpoint);
        $requestBody = $request->getRequestBody();

        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->post($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetFulfillment $response */
            $response = $this->responseParser->parse($rawResponse, GetFulfillment::class);
            $fulfillment = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $fulfillment;
    }

    /**
     * Update fulfillment at the platform.
     *
     * @param FulfillmentRequestInterface $request
     *
     * @return Fulfillment
     * @throws AdapterException
     */
    public function updateFulfillment(FulfillmentRequestInterface $request)
    {
        $uri = sprintf('%s/fulfillments/%s', $this->endpoint, ...$request->getPathParams());
        $requestBody = $request->getRequestBody();

        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->patch($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetFulfillment $response */
            $response = $this->responseParser->parse($rawResponse, GetFulfillment::class);
            $fulfillment = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $fulfillment;
    }
}
