<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\Delivery;

/**
 * Temando Pickup Location Search Request Interface
 *
 * @api
 * @package Temando\Shipping\Api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface PickupLocationSearchRequestInterface
{
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const ACTIVE = 'active';

    /**
     * @return int
     */
    public function getShippingAddressId();

    /**
     * @return bool
     */
    public function isActive();
}
