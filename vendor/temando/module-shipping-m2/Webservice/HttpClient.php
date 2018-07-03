<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice;

use Temando\Shipping\Webservice\Exception\HttpRequestException;
use Temando\Shipping\Webservice\Exception\HttpResponseException;

/**
 * Wrapper around ZF2 HTTP Client
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class HttpClient implements HttpClientInterface
{
    /**
     * @var \Zend\Http\Client
     */
    private $client;

    /**
     * HttpClient constructor.
     * @param \Zend\Http\Client $client
     */
    public function __construct(\Zend\Http\Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string[] $headers
     * @return \Zend\Http\Client
     */
    public function setHeaders(array $headers)
    {
        return $this->client->setHeaders($headers);
    }

    /**
     * @param string $uri
     * @return \Zend\Http\Client
     */
    public function setUri($uri)
    {
        return $this->client->setUri($uri);
    }

    /**
     * @param string[] $options
     * @return \Zend\Http\Client
     */
    public function setOptions(array $options)
    {
        return $this->client->setOptions($options);
    }

    /**
     * @param string $rawBody
     * @return \Zend\Http\Client
     */
    public function setRawBody($rawBody)
    {
        return $this->client->setRawBody($rawBody);
    }

    /**
     * @param string[] $queryParams
     * @return \Zend\Http\Client
     */
    public function setParameterGet($queryParams)
    {
        return $this->client->setParameterGet($queryParams);
    }

    /**
     * @param string $method
     * @return string The response body
     * @throws HttpRequestException
     * @throws HttpResponseException
     */
    public function send($method)
    {
        $this->client->setMethod($method);

        try {
            $response = $this->client->send();
        } catch (\Zend\Http\Exception\RuntimeException $e) {
            throw new HttpRequestException($e->getMessage(), $e->getCode(), $e);
        }

        if (!$response->isSuccess()) {
            throw new HttpResponseException(
                $response->getBody(),
                $response->getStatusCode(),
                null,
                $response->getHeaders()->toString()
            );
        }

        return $response->getBody();
    }
}
