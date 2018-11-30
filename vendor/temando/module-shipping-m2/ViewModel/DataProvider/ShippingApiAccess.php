<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando API Access Provider
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShippingApiAccess implements ShippingApiAccessInterface
{
    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * @var WsConfigInterface
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ApiAccess constructor.
     * @param AuthenticationInterface $auth
     * @param WsConfigInterface $config
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        AuthenticationInterface $auth,
        WsConfigInterface $config,
        UrlInterface $urlBuilder
    ) {
        $this->auth = $auth;
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Obtain Endpoint for Temando REST API access.
     *
     * @return string
     */
    public function getApiEndpoint(): string
    {
        return (string) $this->config->getApiEndpoint();
    }

    /**
     * Obtain Session Token for Temando REST API access and set it if necessary.
     *
     * @return string
     */
    public function getSessionToken(): string
    {
        $bearerToken = $this->config->getBearerToken();
        $accountId   = $this->config->getAccountId();

        try {
            $this->auth->connect($accountId, $bearerToken);
        } catch (LocalizedException $e) {
            return '';
        }

        return (string) $this->auth->getSessionToken();
    }

    /**
     * Obtain Session Token Expiry for Temando REST API access.
     *
     * @return string
     */
    public function getSessionTokenExpiry(): string
    {
        return (string) $this->auth->getSessionTokenExpiry();
    }

    /**
     * Obtain Session Token Retrieval Endpoint
     *
     * @return string
     */
    public function getSessionTokenRefreshEndpoint(): string
    {
        return (string) $this->urlBuilder->getUrl('temando/authentication/token');
    }
}
