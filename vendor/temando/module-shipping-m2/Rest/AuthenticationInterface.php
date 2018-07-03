<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

/**
 * Temando Rest Authentication Service
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface AuthenticationInterface
{
    const DATA_KEY_SESSION_TOKEN = 'temando_api_token';
    const DATA_KEY_SESSION_TOKEN_EXPIRY = 'temando_api_token_expiry';

    const AUTH_SCOPE_ADMIN  = 'admin';

    /**
     * Refresh bearer token.
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function authenticate($username, $password);

    /**
     * Refresh session token if expired.
     *
     * @param string $accountId
     * @param string $bearerToken
     * @return void
     */
    public function connect($accountId, $bearerToken);

    /**
     * Delete session token.
     *
     * @return void
     */
    public function disconnect();

    /**
     * Force refresh session token.
     *
     * @param string $accountId
     * @param string $bearerToken
     * @return void
     */
    public function reconnect($accountId, $bearerToken);

    /**
     * Read Temando Session Token.
     *
     * @return string
     */
    public function getSessionToken();

    /**
     * Read Temando Session Token Expiry Date Time.
     *
     * @return string
     */
    public function getSessionTokenExpiry();
}
