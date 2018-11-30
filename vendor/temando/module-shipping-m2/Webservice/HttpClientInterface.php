<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice;

/**
 * Wrapper around Zend HTTP Client
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface HttpClientInterface
{
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * Set headers.
     *
     * @see \Zend\Http\Client::setHeaders()
     * @see \Zend_Http_Client::setHeaders()
     *
     * @param string[] $headers
     * @return \Zend\Http\Client|\Zend_Http_Client
     */
    public function setHeaders(array $headers);

    /**
     * @see \Zend\Http\Client::setUri()
     * @see \Zend_Http_Client::setUri()
     *
     * @param string $uri
     * @return \Zend\Http\Client|\Zend_Http_Client
     */
    public function setUri($uri);

    /**
     * @see \Zend\Http\Client::setOptions()
     * @see \Zend_Http_Client::setConfig()
     *
     * @param string[] $options
     * @return \Zend\Http\Client|\Zend_Http_Client
     */
    public function setOptions(array $options);

    /**
     * @see \Zend\Http\Client::setRawBody()
     * @see \Zend_Http_Client::setRawData()
     *
     * @param string $rawBody
     * @return \Zend\Http\Client|\Zend_Http_Client
     */
    public function setRawBody($rawBody);

    /**
     * @see \Zend\Http\Client::setParameterGet()
     * @see \Zend_Http_Client::setParameterGet()
     *
     * @param string[] $queryParams
     * @return \Zend\Http\Client|\Zend_Http_Client
     */
    public function setParameterGet($queryParams);

    /**
     * Perform the request and return plain response body.
     *
     * @see \Zend\Http\Client::send()
     * @see \Zend_Http_Client::request()
     *
     * @param string $method
     * @return string The response body
     * @throws \Temando\Shipping\Webservice\Exception\HttpRequestException
     * @throws \Temando\Shipping\Webservice\Exception\HttpResponseException
     */
    public function send($method);
}
