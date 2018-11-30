<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\Api\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Klarna\Core\Api\ServiceInterface;
use Klarna\Core\Model\Api\Exception as KlarnaApiException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Service
 *
 * @package Klarna\Core\Model\Api\Rest
 */
class Service implements ServiceInterface
{
    /**
     * Holds headers to be sent in HTTP request
     *
     * @var array
     */
    private $headers = [];

    /**
     * The base URL to interact with
     *
     * @var string
     */
    private $uri = '';

    /**
     * @var string
     */
    private $username = '';

    /**
     * @var string
     */
    private $password = '';

    /**
     * @var LoggerInterface $log
     */
    private $log;

    /**
     * @var Client
     */
    private $client;

    /**
     * Initialize class
     *
     * @param LoggerInterface $log
     */
    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
        // Client cannot be injected in constructor because Magento Object Manager in 2.1 has problems with it
        $this->client = new Client();
    }

    /**
     * @inheritdoc
     */
    public function setUserAgent($product, $version, $mageInfo)
    {
        $baseUA = sprintf('Guzzle/%s;PHP/%s', \GuzzleHttp\Client::VERSION, PHP_VERSION);
        $this->setHeader(
            'User-Agent',
            sprintf('%s/%s;%s (%s)', $product, $version, $baseUA, $mageInfo)
        );
    }

    /**
     * @inheritdoc
     */
    public function setHeader($header, $value = null)
    {
        if (!$value) {
            unset($this->headers[$header]);
            return;
        }
        $this->headers[$header] = $value;
    }

    /**
     * @inheritdoc
     */
    public function makeRequest($url, $body = '', $method = ServiceInterface::POST, $klarnaId = null)
    {
        $response = [
            'is_successful' => false
        ];
        try {
            $data = [
                'headers' => $this->headers,
                'json'    => $body
            ];
            $data = $this->getAuth($data);

            /** @var ResponseInterface $response */
            $response = $this->client->$method($this->uri . $url, $data);
            $response = $this->processResponse($response);
            $response['is_successful'] = true;
        } catch (BadResponseException $e) {
            $this->log->error('Bad Response: ' . $e->getMessage());
            $this->log->error((string)$e->getRequest()->getBody());
            $response['response_status_code'] = $e->getCode();
            $response['response_status_message'] = $e->getMessage();
            $response = $this->processResponse($response);
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                $this->log->error($errorResponse->getStatusCode() . ' ' . $errorResponse->getReasonPhrase());
                $body = $this->processResponse($errorResponse);
                $response = array_merge($response, $body);
            }
            $response['exception_code'] = $e->getCode();
        } catch (\Exception $e) {
            $this->log->error('Exception: ' . $e->getMessage());
            $response['exception_code'] = $e->getCode();
        }
        if (!$klarnaId) {
            $klarnaId = $this->getKlarnaIdFromResponse($response);
        }
        $this->logRequestResponse($body, $response, $klarnaId, $url);
        return $response;
    }

    /**
     * Set auth data if username or password has been provided
     *
     * @param $data
     * @return mixed
     */
    private function getAuth($data)
    {
        if ($this->username || $this->password) {
            $data['auth'] = [$this->username, $this->password];
        }
        return $data;
    }

    /**
     * Process the response and return an array
     *
     * @param ResponseInterface|array $response
     * @return array
     * @throws \Klarna\Core\Model\Api\Exception
     */
    private function processResponse($response)
    {
        if (is_array($response)) {
            return $response;
        }
        try {
            $data = json_decode((string)$response->getBody(), true);
        } catch (\Exception $e) {
            $data = [
                'exception' => $e->getMessage()
            ];
        }
        if ($response->getStatusCode() === 401) {
            throw new KlarnaApiException(__($response->getReasonPhrase()));
        }
        $data['response_object'] = [
            'headers' => $response->getHeaders(),
            'body'    => $response->getBody()->getContents()
        ];
        $data['response_status_code'] = $response->getStatusCode();
        $data['response_status_message'] = $response->getReasonPhrase();
        return $data;
    }

    /**
     * @param $request
     * @param $response
     * @param $klarnaId
     * @param $url
     */
    private function logRequestResponse($request, $response, $klarnaId, $url)
    {
        $req = [
            'headers' => $this->headers,
            'body'    => $request
        ];

        $context = [
            'klarna_id' => $klarnaId,
            'action'    => $url
        ];

        $this->log->debug(['REQUEST' => $req], $context);
        $this->log->debug(['RESPONSE' => $response], $context);
    }

    /**
     * @inheritdoc
     */
    public function connect($username, $password, $connectUrl = null)
    {
        $this->username = $username;
        $this->password = $password;
        if ($connectUrl) {
            $this->uri = $connectUrl;
        }
        return true;
    }

    /**
     * @param array $response
     * @return string
     */
    private function getKlarnaIdFromResponse($response)
    {
        foreach (['session_id', 'order_id'] as $idField) {
            if (isset($response[$idField])) {
                return $response[$idField];
            }
        }
        return null;
    }
}
