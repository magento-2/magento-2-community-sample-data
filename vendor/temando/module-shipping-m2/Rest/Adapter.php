<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Temando\Shipping\Rest\Adapter\BatchApiInterface;
use Temando\Shipping\Rest\Adapter\CarrierApiInterface;
use Temando\Shipping\Rest\Adapter\CompletionApiInterface;
use Temando\Shipping\Rest\Adapter\ContainerApiInterface;
use Temando\Shipping\Rest\Adapter\EventStreamApiInterface;
use Temando\Shipping\Rest\Adapter\LocationApiInterface;
use Temando\Shipping\Rest\Adapter\ShipmentApiInterface;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Exception\RestClientErrorException;
use Temando\Shipping\Rest\Request\ItemRequestInterface;
use Temando\Shipping\Rest\Request\ListRequestInterface;
use Temando\Shipping\Rest\Request\RequestHeadersInterface;
use Temando\Shipping\Rest\Request\StreamCreateRequestInterface;
use Temando\Shipping\Rest\Request\StreamEventItemRequestInterface;
use Temando\Shipping\Rest\Request\StreamEventListRequestInterface;
use Temando\Shipping\Rest\Response\DataObject\Batch;
use Temando\Shipping\Rest\Response\DataObject\CarrierConfiguration;
use Temando\Shipping\Rest\Response\DataObject\CarrierIntegration;
use Temando\Shipping\Rest\Response\DataObject\Completion;
use Temando\Shipping\Rest\Response\DataObject\Container;
use Temando\Shipping\Rest\Response\DataObject\Location;
use Temando\Shipping\Rest\Response\DataObject\Shipment;
use Temando\Shipping\Rest\Response\DataObject\StreamEvent;
use Temando\Shipping\Rest\Response\DataObject\TrackingEvent;
use Temando\Shipping\Rest\Response\Document\Errors;
use Temando\Shipping\Rest\Response\Document\GetBatch;
use Temando\Shipping\Rest\Response\Document\GetCarrierConfigurations;
use Temando\Shipping\Rest\Response\Document\GetCarrierIntegrations;
use Temando\Shipping\Rest\Response\Document\GetCompletion;
use Temando\Shipping\Rest\Response\Document\GetCompletions;
use Temando\Shipping\Rest\Response\Document\GetContainers;
use Temando\Shipping\Rest\Response\Document\GetLocations;
use Temando\Shipping\Rest\Response\Document\GetShipment;
use Temando\Shipping\Rest\Response\Document\GetStreamEvents;
use Temando\Shipping\Rest\Response\Document\GetTrackingEvents;
use Temando\Shipping\Rest\SchemaMapper\ParserInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando REST API Adapter
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Adapter implements
    BatchApiInterface,
    CarrierApiInterface,
    CompletionApiInterface,
    ContainerApiInterface,
    LocationApiInterface,
    ShipmentApiInterface,
    EventStreamApiInterface
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
     * Adapter constructor.
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
     * @return Batch
     * @throws AdapterException
     */
    public function getBatch(ItemRequestInterface $request)
    {
        $uri = sprintf('%s/shipments/batches/%s', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, [], $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetBatch $response */
            $response = $this->responseParser->parse($rawResponse, GetBatch::class);
            $batch = $response->getData();
            $batch->setShipments($response->getIncluded());
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $batch;
    }

    /**
     * @param ListRequestInterface $request
     * @return CarrierConfiguration[]
     * @throws AdapterException
     */
    public function getCarrierConfigurations(ListRequestInterface $request)
    {
        $uri = sprintf('%s/carriers/configuration', $this->endpoint);
        $queryParams = $request->getRequestParams();

        $this->logger->log(LogLevel::DEBUG, sprintf('%s?%s', $uri, http_build_query($queryParams)));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, $queryParams, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetCarrierConfigurations $response */
            $response = $this->responseParser->parse($rawResponse, GetCarrierConfigurations::class);
            $configurations = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $configurations = [];
        }

        return $configurations;
    }

    /**
     * @param ListRequestInterface $request
     * @return CarrierIntegration[]
     * @throws AdapterException
     */
    public function getCarrierIntegrations(ListRequestInterface $request)
    {
        $uri = sprintf('%s/carriers', $this->endpoint);
        $queryParams = $request->getRequestParams();

        $this->logger->log(LogLevel::DEBUG, sprintf('%s?%s', $uri, http_build_query($queryParams)));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, $queryParams, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetCarrierIntegrations $response */
            $response = $this->responseParser->parse($rawResponse, GetCarrierIntegrations::class);
            $carriers = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $carriers = [];
        }

        return $carriers;
    }

    /**
     * @param ItemRequestInterface $request
     * @return void
     * @throws AdapterException
     */
    public function deleteCarrierConfiguration(ItemRequestInterface $request)
    {
        $uri = sprintf('%s/carriers/configuration/%s', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $this->restClient->delete($uri, $headers);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }
    }

    /**
     * @param ListRequestInterface $request
     * @return Location[]
     * @throws AdapterException
     */
    public function getLocations(ListRequestInterface $request)
    {
        $uri = sprintf('%s/locations', $this->endpoint);
        $queryParams = $request->getRequestParams();

        $this->logger->log(LogLevel::DEBUG, sprintf('%s?%s', $uri, http_build_query($queryParams)));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, $queryParams, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetLocations $response */
            $response = $this->responseParser->parse($rawResponse, GetLocations::class);
            $locations = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $locations = [];
        }

        return $locations;
    }

    /**
     * @param ItemRequestInterface $request
     *
     * @return void
     * @throws AdapterException
     */
    public function deleteLocation(ItemRequestInterface $request)
    {
        $uri = sprintf('%s/locations/%s', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $this->restClient->delete($uri, $headers);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage(), ['exception' => $e]);

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }
    }

    /**
     * @param ListRequestInterface $request
     * @return Container[]
     * @throws AdapterException
     */
    public function getContainers(ListRequestInterface $request)
    {
        $uri = sprintf('%s/containers', $this->endpoint);
        $queryParams = $request->getRequestParams();

        $this->logger->log(LogLevel::DEBUG, sprintf('%s?%s', $uri, http_build_query($queryParams)));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, $queryParams, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetContainers $response */
            $response = $this->responseParser->parse($rawResponse, GetContainers::class);
            $containers = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $containers = [];
        }

        return $containers;
    }

    /**
     * @param ItemRequestInterface $request
     *
     * @return void
     * @throws AdapterException
     */
    public function deleteContainer(ItemRequestInterface $request)
    {
        $uri = sprintf('%s/containers/%s', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $this->restClient->delete($uri, $headers);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }
    }

    /**
     * @param ListRequestInterface $request
     * @return Completion[]
     * @throws AdapterException
     */
    public function getCompletions(ListRequestInterface $request)
    {
        $uri = sprintf('%s/completions', $this->endpoint);
        $queryParams = $request->getRequestParams();
        $queryParams['filter'] = rawurlencode(json_encode($queryParams['filter']));

        $this->logger->log(LogLevel::DEBUG, sprintf('%s?%s', $uri, http_build_query($queryParams)));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, $queryParams, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetCompletions $response */
            $response = $this->responseParser->parse($rawResponse, GetCompletions::class);
            $completions  = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $completions = [];
        }

        return $completions;
    }

    /**
     * @param ItemRequestInterface $request
     * @return Shipment
     * @throws AdapterException
     */
    public function getShipment(ItemRequestInterface $request)
    {
        $uri = sprintf('%s/shipments/%s', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, [], $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetShipment $response */
            $response = $this->responseParser->parse($rawResponse, GetShipment::class);
            $shipment = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $shipment;
    }

    /**
     * @param ItemRequestInterface $request
     * @return TrackingEvent[]
     * @throws AdapterException
     */
    public function getTrackingEvents(ItemRequestInterface $request)
    {
        $uri = sprintf('%s/shipments/%s/tracking', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, [], $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetTrackingEvents $response */
            $response = $this->responseParser->parse($rawResponse, GetTrackingEvents::class);
            $trackingEvents = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $trackingEvents;
    }

    /**
     * @param ItemRequestInterface $request
     * @return Completion
     * @throws AdapterException
     */
    public function getCompletion(ItemRequestInterface $request)
    {
        $uri = sprintf('%s/completions/%s', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, [], $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetCompletion $response */
            $response = $this->responseParser->parse($rawResponse, GetCompletion::class);
            $completion = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $completion;
    }

    /**
     * @param StreamCreateRequestInterface $request
     * @return void
     * @throws AdapterException
     */
    public function createStream(StreamCreateRequestInterface $request)
    {
        $uri = sprintf('%s/streams', $this->endpoint);
        $requestBody = $request->getRequestBody();

        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->post($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }
    }

    /**
     * @param ItemRequestInterface $request
     * @return void
     * @throws AdapterException
     */
    public function deleteStream(ItemRequestInterface $request)
    {
        $uri = sprintf('%s/streams/%s', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $this->restClient->delete($uri, $headers);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }
    }

    /**
     * @param StreamEventListRequestInterface $request
     *
     * @return StreamEvent[]
     * @throws AdapterException
     */
    public function getStreamEvents(StreamEventListRequestInterface $request)
    {
        $uri = sprintf('%s/streams/%s/events', $this->endpoint, ...$request->getPathParams());
        $queryParams = $request->getRequestParams();

        $this->logger->log(LogLevel::DEBUG, sprintf('%s?%s', $uri, http_build_query($queryParams)));

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $rawResponse = $this->restClient->get($uri, $queryParams, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var GetStreamEvents $response */
            $response = $this->responseParser->parse($rawResponse, GetStreamEvents::class);
            $events = $response->getData();
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $events = [];
        }

        return $events;
    }

    /**
     * @param StreamEventItemRequestInterface $request
     * @return void
     * @throws AdapterException
     */
    public function deleteStreamEvent(StreamEventItemRequestInterface $request)
    {
        $uri = sprintf('%s/streams/%s/events/%s', $this->endpoint, ...$request->getPathParams());

        $this->logger->log(LogLevel::DEBUG, $uri);

        try {
            $this->auth->connect($this->accountId, $this->bearerToken);
            $headers = $this->requestHeaders->getHeaders();

            $this->restClient->delete($uri, $headers);
        } catch (RestClientErrorException $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());

            /** @var Errors $response */
            $response = $this->responseParser->parse($e->getMessage(), Errors::class);
            throw AdapterException::errorResponse($response, $e);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }
    }
}
