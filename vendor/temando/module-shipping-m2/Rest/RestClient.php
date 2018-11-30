<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

use Temando\Shipping\Rest\Exception\RestClientErrorException;
use Temando\Shipping\Rest\Exception\RestException;
use Temando\Shipping\Rest\Exception\RestResponseException;
use Temando\Shipping\Webservice\Exception\HttpException;
use Temando\Shipping\Webservice\HttpClientInterface;
use Temando\Shipping\Webservice\HttpClientInterfaceFactory;

/**
 * Temando HTTP REST Client
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class RestClient implements RestClientInterface
{
    /**
     * @var HttpClientInterfaceFactory
     */
    private $httpClientFactory;

    /**
     * RestClient constructor.
     * @param HttpClientInterfaceFactory $httpClientFactory
     */
    public function __construct(HttpClientInterfaceFactory $httpClientFactory)
    {
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * @param string $uri
     * @param string $rawBody
     * @param string[] $headers
     * @return string
     * @throws RestException
     */
    public function post($uri, $rawBody, array $headers)
    {
        $httpClient = $this->httpClientFactory->create();
        $httpClient->setHeaders($headers);
        $httpClient->setUri($uri);
        $httpClient->setOptions(['trace' => 1, 'maxredirects' => 0, 'timeout' => 30, 'useragent' => 'M2']);
        $httpClient->setRawBody($rawBody);

        try {
            $response = $httpClient->send(HttpClientInterface::METHOD_POST);
        } catch (HttpException $e) {
            $errorCode = $e->getCode();
            if ($errorCode < 500 && $errorCode > 401) {
                // handle client errors with parseable content
                throw new RestClientErrorException($e->getMessage(), $e->getCode(), $e);
            } else {
                throw new RestResponseException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $response;
    }

    /**
     * @param string $uri
     * @param string $rawBody
     * @param string[] $headers
     * @return string
     * @throws RestException
     */
    public function put($uri, $rawBody, array $headers)
    {
        $httpClient = $this->httpClientFactory->create();
        $httpClient->setHeaders($headers);
        $httpClient->setUri($uri);
        $httpClient->setOptions(['trace' => 1, 'maxredirects' => 0, 'timeout' => 30, 'useragent' => 'M2']);
        $httpClient->setRawBody($rawBody);

        try {
            $response = $httpClient->send(HttpClientInterface::METHOD_PUT);
        } catch (HttpException $e) {
            $errorCode = $e->getCode();
            if ($errorCode < 500 && $errorCode >= 400) {
                // handle client errors with parseable content
                throw new RestClientErrorException($e->getMessage(), $e->getCode(), $e);
            } else {
                throw new RestResponseException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $response;
    }

    /**
     * @param string $uri
     * @param string $rawBody
     * @param string[] $headers
     * @return string
     * @throws RestException
     */
    public function patch($uri, $rawBody, array $headers)
    {
        $httpClient = $this->httpClientFactory->create();
        $httpClient->setHeaders($headers);
        $httpClient->setUri($uri);
        $httpClient->setOptions(['trace' => 1, 'maxredirects' => 0, 'timeout' => 30, 'useragent' => 'M2']);
        $httpClient->setRawBody($rawBody);

        try {
            $response = $httpClient->send(HttpClientInterface::METHOD_PATCH);
        } catch (HttpException $e) {
            $errorCode = $e->getCode();
            if ($errorCode < 500 && $errorCode >= 400) {
                // handle client errors with parseable content
                throw new RestClientErrorException($e->getMessage(), $e->getCode(), $e);
            } else {
                throw new RestResponseException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $response;
    }

    /**
     * @param string $uri
     * @param string[] $queryParams
     * @param string[] $headers
     * @return string
     * @throws RestException
     */
    public function get($uri, array $queryParams, array $headers)
    {
        $httpClient = $this->httpClientFactory->create();
        $httpClient->setHeaders($headers);
        $httpClient->setUri($uri);
        $httpClient->setOptions(['trace' => 1, 'maxredirects' => 0, 'timeout' => 30, 'useragent' => 'M2']);
        $httpClient->setParameterGet($queryParams);

        try {
            $response = $httpClient->send(HttpClientInterface::METHOD_GET);
        } catch (HttpException $e) {
            $errorCode = $e->getCode();
            if ($errorCode < 500 && $errorCode >= 400) {
                // handle client errors with parseable content
                throw new RestClientErrorException($e->getMessage(), $e->getCode(), $e);
            } else {
                throw new RestResponseException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $response;
    }

    /**
     * @param string $uri
     * @param string[] $headers
     *
     * @return string
     * @throws RestClientErrorException
     * @throws RestResponseException
     */
    public function delete($uri, array $headers)
    {
        $httpClient = $this->httpClientFactory->create();
        $httpClient->setHeaders($headers);
        $httpClient->setUri($uri);
        $httpClient->setOptions(['trace' => 1, 'maxredirects' => 0, 'timeout' => 30, 'useragent' => 'M2']);

        try {
            $response = $httpClient->send(HttpClientInterface::METHOD_DELETE);
        } catch (HttpException $e) {
            $errorCode = $e->getCode();
            if ($errorCode < 500 && $errorCode >= 400) {
                // handle client errors with parseable content
                throw new RestClientErrorException($e->getMessage(), $e->getCode(), $e);
            } else {
                throw new RestResponseException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $response;
    }
}
