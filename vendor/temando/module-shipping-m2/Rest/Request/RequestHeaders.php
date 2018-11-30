<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request;

use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando API Request Headers
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class RequestHeaders implements RequestHeadersInterface
{
    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * Adapter constructor.
     * @param WsConfigInterface $config
     * @param AuthenticationInterface $auth,
     */
    public function __construct(
        WsConfigInterface $config,
        AuthenticationInterface $auth
    ) {
        $this->endpoint = $config->getApiEndpoint();
        $this->apiVersion = $config->getApiVersion();
        $this->auth = $auth;
    }

    /**
     * @return string[]
     */
    public function getHeaders()
    {
        return [
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'application/vnd.api+json',
            'Accept'        => 'application/vnd.api+json',
            'Origin'        => $this->endpoint,
            'Version'       => $this->apiVersion,
            'Authorization' => sprintf('Bearer %s', $this->auth->getSessionToken()),
        ];
    }
}
