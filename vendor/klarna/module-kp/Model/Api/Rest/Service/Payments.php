<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Api\Rest\Service;

use Klarna\Core\Api\ServiceInterface;
use Klarna\Core\Helper\ConfigHelper;
use Klarna\Core\Helper\KlarnaConfig;
use Klarna\Core\Helper\VersionInfo;
use Klarna\Core\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Model\Api\Response;
use Klarna\Kp\Model\Api\ResponseFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Payments
 *
 * @package Klarna\Kp\Model\Api\Rest\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Payments implements CreditApiInterface
{
    const API_VERSION = 'v1';

    /**
     * @var ServiceInterface
     */
    private $service;
    /**
     * @var VersionInfo
     */
    private $versionInfo;
    /**
     * @var LoggerInterface $log
     */
    private $log;
    /**
     * @var StoreInterface
     */
    private $store;
    /**
     * @var ConfigHelper
     */
    private $configHelper;
    /**
     * @var ResponseFactory
     */
    private $responseFactory;
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var KlarnaConfig
     */
    private $klarnaConfig;

    /**
     * Kasper constructor.
     *
     * @param ScopeConfigInterface  $config
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface       $log
     * @param VersionInfo           $versionInfo
     * @param ResponseFactory       $responseFactory
     * @param ConfigHelper          $configHelper
     * @param KlarnaConfig          $klarnaConfig
     * @param ServiceInterface      $service
     */
    public function __construct(
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        LoggerInterface $log,
        VersionInfo $versionInfo,
        ResponseFactory $responseFactory,
        ConfigHelper $configHelper,
        KlarnaConfig $klarnaConfig,
        ServiceInterface $service
    ) {
        $this->log = $log;
        $this->service = $service;
        $this->responseFactory = $responseFactory;
        $this->store = $storeManager->getStore();
        $this->config = $config;
        $this->versionInfo = $versionInfo;
        $this->configHelper = $configHelper;
        $this->klarnaConfig = $klarnaConfig;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws \Klarna\Core\Exception
     */
    public function createSession(RequestInterface $request)
    {
        return $this->processRequest('/payments/' . self::API_VERSION . '/sessions', $request);
    }

    /**
     * @param string           $url
     * @param RequestInterface $request
     * @param string           $method
     * @param string           $klarnaId
     * @return Response
     * @throws \Klarna\Core\Exception
     */
    private function processRequest(
        $url,
        RequestInterface $request = null,
        $method = ServiceInterface::POST,
        $klarnaId = null
    ) {
        $body = '';
        if ($request) {
            $body = $request->toArray();
        }
        $this->connect();
        $response = $this->service->makeRequest($url, $body, $method, $klarnaId);
        $response['response_code'] = $response['response_status_code'];
        return $this->responseFactory->create(['data' => $response]);
    }

    /**
     * @return string
     * @throws \Klarna\Core\Exception
     */
    private function connect()
    {
        $version = sprintf(
            '%s;Core/%s;OM/%s',
            $this->versionInfo->getVersion('Klarna_Kp'),
            $this->versionInfo->getVersion('Klarna_Core'),
            $this->versionInfo->getVersion('Klarna_Ordermanagement')
        );
        $mageMode = $this->versionInfo->getMageMode();
        $mageVersion = $this->versionInfo->getMageEdition() . '/' . $this->versionInfo->getMageVersion();
        $mageInfo = "Magento {$mageVersion} {$mageMode} mode";
        $this->service->setUserAgent('Magento2_KP', $version, $mageInfo);
        $this->service->setHeader('Accept', '*/*');

        $username = $this->config->getValue('klarna/api/merchant_id', ScopeInterface::SCOPE_STORES, $this->store);
        $password = $this->config->getValue('klarna/api/shared_secret', ScopeInterface::SCOPE_STORES, $this->store);
        $test_mode = $this->config->getValue('klarna/api/test_mode', ScopeInterface::SCOPE_STORES, $this->store);

        $versionConfig = $this->klarnaConfig->getVersionConfig($this->store);
        $url = $versionConfig->getUrl($test_mode);

        $this->service->connect($username, $password, $url);
    }

    /**
     * @param string           $sessionId
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws \Klarna\Core\Exception
     */
    public function updateSession($sessionId, RequestInterface $request)
    {
        $response = $this->processRequest(
            '/payments/' . self::API_VERSION . '/sessions/' . $sessionId,
            $request,
            ServiceInterface::POST,
            $sessionId
        );
        if ($response->getResponseCode() === 204) {
            return $this->readSession($sessionId);
        }
        return $response;
    }

    /**
     * @param string           $sessionId
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws \Klarna\Core\Exception
     */
    public function readSession($sessionId)
    {
        $resp = $this->processRequest(
            '/payments/' . self::API_VERSION . '/sessions/' . $sessionId,
            null,
            ServiceInterface::GET,
            $sessionId
        );
        $response = $resp->toArray();
        $response['session_id'] = $sessionId;
        return $this->responseFactory->create(['data' => $response]);
    }

    /**
     * @param string           $authorization_token
     * @param RequestInterface $request
     * @param null             $klarnaId
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws \Klarna\Core\Exception
     */
    public function placeOrder($authorization_token, RequestInterface $request, $klarnaId = null)
    {
        return $this->processRequest(
            '/payments/' . self::API_VERSION . '/authorizations/' . $authorization_token . '/order',
            $request,
            ServiceInterface::POST,
            $klarnaId
        );
    }

    /**
     * @param string $authorization_token
     * @param null   $klarnaId
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws \Klarna\Core\Exception
     */
    public function cancelOrder($authorization_token, $klarnaId = null)
    {
        return $this->processRequest(
            '/payments/' . self::API_VERSION . '/authorizations/' . $authorization_token,
            null,
            ServiceInterface::DELETE,
            $klarnaId
        );
    }
}
