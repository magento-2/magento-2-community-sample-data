<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Framework\DataObject;
use Temando\Shipping\Model\Shipment\Location;

/**
 * Temando Pickup Entity
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Pickup extends DataObject implements PickupInterface
{
    /**
     * @return string
     */
    public function getPickupId()
    {
        return $this->getData(self::PICKUP_ID);
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @return string
     */
    public function getReadyAt()
    {
        return $this->getData(self::READY_AT);
    }

    /**
     * @return string
     */
    public function getPickedUpAt()
    {
        return $this->getData(self::PICKED_UP_AT);
    }

    /**
     * @return string
     */
    public function getCancelledAt()
    {
        return $this->getData(self::CANCELLED_AT);
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * @return Location
     */
    public function getPickupLocation()
    {
        return $this->getData(self::PICKUP_LOCATION);
    }

    /**
     * @return string
     */
    public function getSalesOrderId()
    {
        return $this->getData(self::SALES_ORDER_ID);
    }

    /**
     * @return string
     */
    public function getOrderReference()
    {
        return $this->getData(self::ORDER_REFERENCE);
    }

    /**
     * @return string
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * format: [ <sku> => <qty>, <sku> => <qty> ]
     * @return string[]
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS);
    }
}
