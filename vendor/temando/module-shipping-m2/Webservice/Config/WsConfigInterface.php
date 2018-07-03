<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Config;

/**
 * Temando REST API Config Interface
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface WsConfigInterface
{
    /**
     * Check if webservice communication logging is enabled.
     *
     * @return bool
     */
    public function isLoggingEnabled();

    /**
     * Read URL of Temando Authentication API.
     *
     * @return string
     */
    public function getSessionEndpoint();

    /**
     * Read URL of Temando REST API.
     *
     * @return string
     */
    public function getApiEndpoint();

    /**
     * Save URL of Temando REST API.
     *
     * @param string $apiEndpoint
     * @return void
     */
    public function saveApiEndpoint($apiEndpoint);

    /**
     * Obtain the API version to connect to.
     *
     * @return string
     */
    public function getApiVersion();

    /**
     * Read Temando Account Id.
     *
     * @return string
     */
    public function getAccountId();

    /**
     * Read Temando Authentication Token.
     *
     * @return string
     */
    public function getBearerToken();

    /**
     * Read Temando Authentication Token Expiry Timestamp.
     *
     * @return string
     */
    public function getBearerTokenExpiry();

    /**
     * Save new account data.
     *
     * @param string $accountId
     * @param string $bearerToken
     * @param string $bearerTokenExpiry
     * @return void
     */
    public function setAccount($accountId, $bearerToken, $bearerTokenExpiry);

    /**
     * Unset all account data.
     *
     * @return void
     */
    public function unsetAccount();
}
