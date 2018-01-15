<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Temando\Shipping\Rest\Adapter\AuthenticationApiInterface;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\AuthRequestInterfaceFactory;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando REST API Authentication
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Authentication implements AuthenticationInterface
{
    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var WsConfigInterface
     */
    private $config;

    /**
     * @var AuthenticationApiInterface
     */
    private $apiAdapter;

    /**
     * @var AuthRequestInterfaceFactory
     */
    private $authRequestFactory;

    /**
     * @var DateTime
     */
    private $datetime;

    /**
     * Authentication constructor.
     *
     * @param SessionManagerInterface $session
     * @param WsConfigInterface $config
     * @param AuthenticationApiInterface $apiAdapter
     * @param AuthRequestInterfaceFactory $authRequestFactory
     * @param DateTime $datetime
     */
    public function __construct(
        SessionManagerInterface $session,
        WsConfigInterface $config,
        AuthenticationApiInterface $apiAdapter,
        AuthRequestInterfaceFactory $authRequestFactory,
        DateTime $datetime
    ) {
        $this->apiAdapter         = $apiAdapter;
        $this->config             = $config;
        $this->authRequestFactory = $authRequestFactory;
        $this->session            = $session;
        $this->datetime           = $datetime;
    }

    /**
     * Check if Session Token is invalid
     *
     * @return bool
     */
    private function isSessionTokenExpired()
    {
        $sessionTokenExpiry = strtotime($this->getSessionTokenExpiry());
        $currentTime = $this->datetime->timestamp();
        $threshold = 1200; //20min in s

        return (($sessionTokenExpiry - $threshold ) < $currentTime);
    }

    /**
     * Save Temando API token to admin session.
     *
     * @param string $sessionToken
     * @param string $sessionTokenExpiry
     * @return void
     */
    private function setSession($sessionToken, $sessionTokenExpiry)
    {
        $this->session->setData(self::DATA_KEY_SESSION_TOKEN, $sessionToken);
        $this->session->setData(self::DATA_KEY_SESSION_TOKEN_EXPIRY, $sessionTokenExpiry);
    }

    /**
     * Remove Temando API token from admin session.
     *
     * @return void
     */
    private function unsetSession()
    {
        $this->session->unsetData(self::DATA_KEY_SESSION_TOKEN);
        $this->session->unsetData(self::DATA_KEY_SESSION_TOKEN_EXPIRY);
    }

    /**
     * Refresh bearer token.
     * For future use, bearer tokens do currently not expire.
     *
     * @param string $username
     * @param string $password
     * @return void
     * @throws AuthenticationException
     * @throws InputException
     */
    public function authenticate($username, $password)
    {
        if (!$username) {
            throw InputException::requiredField('username');
        }

        if (!$password) {
            throw InputException::requiredField('password');
        }

        try {
            $requestType = $this->authRequestFactory->create([
                'scope' => self::AUTH_SCOPE_ADMIN,
                'username' => $username,
                'password' => $password,
            ]);

            $this->apiAdapter->startSession($requestType);
        } catch (AdapterException $e) {
            $msg = 'API connection could not be established. Please check your credentials (%1).';
            throw new AuthenticationException(__($msg, $e->getMessage()), $e);
        }
    }

    /**
     * Refresh session token if expired.
     *
     * @param string $accountId
     * @param string $bearerToken
     * @return void
     * @throws AuthenticationException
     * @throws InputException
     */
    public function connect($accountId, $bearerToken)
    {
        if (!$this->isSessionTokenExpired()) {
            return;
        }

        if (!$accountId) {
            throw InputException::requiredField('accountId');
        }

        if (!$bearerToken) {
            throw InputException::requiredField('bearerToken');
        }

        try {
            $requestType = $this->authRequestFactory->create([
                'scope'       => self::AUTH_SCOPE_ADMIN,
                'accountId'   => $accountId,
                'bearerToken' => $bearerToken,
            ]);
            $session = $this->apiAdapter->startSession($requestType);
        } catch (AdapterException $e) {
            $msg = 'API connection could not be established. Please check your credentials (%1).';
            throw new AuthenticationException(__($msg, $e->getMessage()), $e);
        }

        // save session info in admin/customer session
        $this->setSession(
            $session->getAttributes()->getSessionToken(),
            $session->getAttributes()->getExpiry()
        );

        // save merchant's api endpoint in config
        if ($session->getAttributes()->getApiUrl()) {
            $this->config->saveApiEndpoint($session->getAttributes()->getApiUrl());
        } else {
            $this->config->saveApiEndpoint($this->config->getSessionEndpoint());
        }
    }

    /**
     * Delete session token.
     *
     * @return void
     */
    public function disconnect()
    {
        $this->apiAdapter->endSession();
        $this->unsetSession();
    }

    /**
     * Force refresh session token.
     *
     * @param string $accountId
     * @param string $bearerToken
     * @return void
     * @throws AuthenticationException
     * @throws InputException
     */
    public function reconnect($accountId, $bearerToken)
    {
        $this->disconnect();
        $this->connect($accountId, $bearerToken);
    }

    /**
     * Read Temando Session Token.
     *
     * @return string
     */
    public function getSessionToken()
    {
        return $this->session->getData(self::DATA_KEY_SESSION_TOKEN);
    }

    /**
     * Read Temando Session Token Expiry Date Time.
     *
     * @return string
     */
    public function getSessionTokenExpiry()
    {
        return $this->session->getData(self::DATA_KEY_SESSION_TOKEN_EXPIRY);
    }
}
