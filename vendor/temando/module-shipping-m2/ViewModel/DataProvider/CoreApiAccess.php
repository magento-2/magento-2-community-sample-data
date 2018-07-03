<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\Auth\StorageInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Integration\Model\Oauth\Token;
use Magento\Security\Model\Config;

/**
 * M2 Core API Access Provider
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CoreApiAccess implements CoreApiAccessInterface
{
    /**
     * @var StorageInterface|Session
     */
    private $session;

    /**
     * @var Config
     */
    private $securityConfig;

    /**
     * @var Token
     */
    private $token;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * ApiAccess constructor.
     * @param StorageInterface $session
     * @param Config $securityConfig
     * @param DateTime $dateTime
     * @param Token $token
     */
    public function __construct(
        StorageInterface $session,
        Config $securityConfig,
        Token $token,
        DateTime $dateTime
    ) {
        $this->session = $session;
        $this->securityConfig = $securityConfig;
        $this->token = $token;
        $this->dateTime = $dateTime;
    }

    /**
     * Obtain authentication token for Magento REST API access.
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        $adminId = $this->session->getUser()->getId();
        $token   = $this->token->loadByAdminId($adminId)->getToken();
        if (!$token) {
            $token = $this->token->createAdminToken($adminId)->getToken();
        } else {
            $this->token->setCreatedAt($this->dateTime->gmtDate());
            $this->token->save();
        }

        return (string) $token;
    }

    /**
     * Obtain admin session expiration timestamp.
     *
     * @return int
     */
    public function getSessionExpirationTime(): int
    {
        $sessionStart = $this->session->getUpdatedAt();
        $sessionDuration = $this->securityConfig->getAdminSessionLifetime();

        return $sessionStart + $sessionDuration;
    }
}
