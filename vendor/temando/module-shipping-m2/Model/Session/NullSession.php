<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Session;

use Magento\Framework\DataObject;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Temando Shipping Carrier
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class NullSession extends DataObject implements SessionManagerInterface
{
    /**
     * Start session
     *
     * @return SessionManagerInterface
     */
    public function start()
    {
        return $this;
    }

    /**
     * Session write close
     *
     * @return void
     */
    public function writeClose()
    {
    }

    /**
     * Does a session exist
     *
     * @return bool
     */
    public function isSessionExists()
    {
        return true;
    }

    /**
     * Retrieve session Id
     *
     * @return string
     */
    public function getSessionId()
    {
        return "1";
    }

    /**
     * Retrieve session name
     *
     * @return string
     */
    public function getName()
    {
        return "NullSession";
    }

    /**
     * Set session name
     *
     * @param string $name
     *
     * @return SessionManagerInterface
     */
    public function setName($name)
    {
        return $this;
    }

    /**
     * Destroy/end a session
     *
     * @param  array $options
     *
     * @return void
     */
    public function destroy(array $options = null)
    {
    }

    /**
     * Unset session data
     *
     * @return $this
     */
    public function clearStorage()
    {
        return $this;
    }

    /**
     * Retrieve Cookie domain
     *
     * @return string
     */
    public function getCookieDomain()
    {
        return "NullCookieDomain";
    }

    /**
     * Retrieve cookie path
     *
     * @return string
     */
    public function getCookiePath()
    {
        return "NullCookiePath";
    }

    /**
     * Retrieve cookie lifetime
     *
     * @return int
     */
    public function getCookieLifetime()
    {
        return 111111111;
    }

    /**
     * Specify session identifier
     *
     * @param string|null $sessionId
     *
     * @return SessionManagerInterface
     */
    public function setSessionId($sessionId)
    {
        return $this;
    }

    /**
     * Renew session id and update session cookie
     *
     * @return SessionManagerInterface
     */
    public function regenerateId()
    {
        return $this;
    }

    /**
     * Expire the session cookie
     *
     * Sends a session cookie with no value, and with an expiry in the past.
     *
     * @return void
     */
    public function expireSessionCookie()
    {
    }

    /**
     * If session cookie is not applicable due to host or path mismatch - add session id to query
     *
     * @param string $urlHost
     *
     * @return string
     */
    public function getSessionIdForHost($urlHost)
    {
        return "NullSessionIdForHosts";
    }

    /**
     * Check if session is valid for given hostname
     *
     * @param string $host
     *
     * @return bool
     */
    public function isValidForHost($host)
    {
        return true;
    }

    /**
     * Check if session is valid for given path
     *
     * @param string $path
     *
     * @return bool
     */
    public function isValidForPath($path)
    {
        return true;
    }
}
