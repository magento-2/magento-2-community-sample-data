<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

/**
 * API Access Provider Interface
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ShippingApiAccessInterface
{
    /**
     * Obtain Endpoint for Temando REST API access.
     *
     * @return string
     */
    public function getApiEndpoint(): string;

    /**
     * Obtain Session Token for Temando REST API access and set it if necessary.
     *
     * @return string
     */
    public function getSessionToken(): string;

    /**
     * Obtain Session Token Expiry for Temando REST API access.
     *
     * @return string
     */
    public function getSessionTokenExpiry(): string;

    /**
     * Obtain Session Token Retrieval Endpoint
     *
     * @return string
     */
    public function getSessionTokenRefreshEndpoint(): string;
}
