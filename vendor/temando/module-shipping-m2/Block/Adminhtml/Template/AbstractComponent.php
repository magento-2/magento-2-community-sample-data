<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Template;

use Magento\Backend\Block\Widget\Container as WidgetContainer;
use Magento\Backend\Block\Widget\Context as WidgetContext;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\Auth\StorageInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Integration\Model\Oauth\Token;
use Magento\Security\Model\Config;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Default block for all pages that display Temando components
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 * @deprecated since 1.0.3 | Child blocks will be migrated one after the other.
 * @see \Temando\Shipping\Block\Adminhtml\ComponentContainer
 */
abstract class AbstractComponent extends WidgetContainer
{
    /**
     * @var WsConfigInterface
     */
    private $config;

    /**
     * @var StorageInterface|Session
     */
    private $session;

    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * @var token
     */
    private $token;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var Config
     */
    private $securityConfig;

    /**
     * AbstractComponent constructor.
     *
     * @param WidgetContext $context
     * @param WsConfigInterface $config
     * @param StorageInterface $session
     * @param AuthenticationInterface $auth
     * @param Token $token
     * @param DateTime $dateTime
     * @param RemoteAddress $remoteAddress
     * @param Config $securityConfig
     * @param array $data
     */
    public function __construct(
        WidgetContext $context,
        WsConfigInterface $config,
        StorageInterface $session,
        AuthenticationInterface $auth,
        Token $token,
        DateTime $dateTime,
        RemoteAddress $remoteAddress,
        Config $securityConfig,
        array $data = []
    ) {
        $this->config         = $config;
        $this->session        = $session;
        $this->auth           = $auth;
        $this->token          = $token;
        $this->dateTime       = $dateTime;
        $this->remoteAddress  = $remoteAddress;
        $this->securityConfig = $securityConfig;

        parent::__construct($context, $data);
    }

    /**
     * Obtain authentication token for Magento REST API access.
     *
     * @return string
     */
    public function getAccessToken()
    {
        $adminId = $this->session->getUser()->getId();
        $token   = $this->token->loadByAdminId($adminId)->getToken();
        if (!$token) {
            $token = $this->token->createAdminToken($adminId)->getToken();
        } else {
            $this->token->setCreatedAt($this->dateTime->gmtDate());
            $this->token->save();
        }

        return $token;
    }

    /**
     * Obtain Endpoint for Temando REST API access.
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->config->getApiEndpoint();
    }

    /**
     * Obtain Access Token for Temando REST API access.
     *
     * @return string
     */
    public function getBearerToken()
    {
        return $this->config->getBearerToken();
    }

    /**
     * Obtain Access Token expiry timestamp for Temando REST API access.
     *
     * @return string
     */
    public function getBearerTokenExpiry()
    {
        return $this->config->getBearerTokenExpiry();
    }

    /**
     * Obtain Session Token for Temando REST API access and set it if necessary.
     *
     * @return string
     */
    public function getApiToken()
    {
        $bearerToken = $this->config->getBearerToken();
        $accountId   = $this->config->getAccountId();

        try {
            $this->auth->connect($accountId, $bearerToken);
        } catch (LocalizedException $e) {
            return '';
        }

        return $this->auth->getSessionToken();
    }

    /**
     * Obtain Session Token Expiry for Temando REST API access.
     *
     * @return string
     */
    public function getApiTokenExpiry()
    {
        return $this->auth->getSessionTokenExpiry();
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        $localeCode = $this->_scopeConfig->getValue(
            DirectoryHelper::XML_PATH_DEFAULT_LOCALE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        return strtolower(str_replace('_', '-', $localeCode));
    }

    /**
     * Obtain Language Code.
     *
     * @return string
     */
    public function getLanguage()
    {
        return substr_replace($this->getLocale(), '', 2);
    }

    /**
     * Obtain componentry assets base url.
     *
     * @return string
     */
    public function getAssetsUrl()
    {
        return $this->getViewFileUrl('Temando_Shipping') . '/';
    }

    /**
     * Obtain Magento HTTP endpoint for session token retrieval.
     *
     * @return string
     */
    public function getApiTokenRefreshEndpoint()
    {
        return $this->_urlBuilder->getUrl('temando/authentication/token');
    }

    /**
     * Obtain merchant IP address.
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * @return int
     */
    public function getSessionExpirationTime()
    {
        $sessionStart = $this->session->getUpdatedAt();
        $sessionDuration = $this->securityConfig->getAdminSessionLifetime();

        return $sessionStart + $sessionDuration;
    }
}
