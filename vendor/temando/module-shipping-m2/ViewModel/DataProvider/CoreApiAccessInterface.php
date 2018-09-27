<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

/**
 * M2 Core API Access Provider Interface
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CoreApiAccessInterface
{
    /**
     * Obtain authentication token for Magento REST API access.
     *
     * @return string
     */
    public function getAccessToken(): string;

    /**
     * Obtain admin session expiration timestamp.
     *
     * @return int
     */
    public function getSessionExpirationTime(): int;
}
