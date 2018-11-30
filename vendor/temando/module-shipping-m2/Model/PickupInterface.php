<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model;

use Temando\Shipping\Model\Shipment\Location;

/**
 * Temando Pickup Interface.
 *
 * The pickup data object represents one item in the pickup
 * grid listing or on the pickup details page.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface PickupInterface
{
    const PICKUP_ID = 'pickup_id';
    const STATE = 'state';
    const ORDER_ID = 'order_id';
    const SALES_ORDER_ID = 'sales_order_id';
    const ORDER_REFERENCE = 'order_reference';
    const CREATED_AT = 'created_at';
    const READY_AT = 'ready_at';
    const PICKED_UP_AT = 'picked_up_at';
    const CANCELLED_AT = 'cancelled_at';
    const CUSTOMER_NAME = 'customer_name';
    const PICKUP_LOCATION = 'pickup_location';
    const LOCATION_ID = 'location_id';
    const ITEMS = 'items';

    const STATE_REQUESTED = 'pickup requested';
    const STATE_READY = 'ready for pickup';
    const STATE_PICKED_UP = 'picked up';
    const STATE_CANCELLED = 'cancelled';

    /**
     * @return string
     */
    public function getPickupId();

    /**
     * @return string
     */
    public function getState();

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getReadyAt();

    /**
     * @return string
     */
    public function getPickedUpAt();

    /**
     * @return string
     */
    public function getCancelledAt();

    /**
     * @return string
     */
    public function getCustomerName();

    /**
     * @return Location
     */
    public function getPickupLocation();

    /**
     * @return string
     */
    public function getSalesOrderId();

    /**
     * @return string
     */
    public function getOrderReference();

    /**
     * @return string
     */
    public function getLocationId();

    /**
     * @return string[]
     */
    public function getItems();
}
