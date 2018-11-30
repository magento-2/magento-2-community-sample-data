<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\Delivery;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Temando\Shipping\Api\Data\Delivery\OrderPickupLocationInterface;
use Temando\Shipping\Model\ResourceModel\Db\NoSequenceDb;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Order Pickup Location Resource Model
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderPickupLocation extends NoSequenceDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SetupSchema::TABLE_ORDER_PICKUP_LOCATION, OrderPickupLocationInterface::RECIPIENT_ADDRESS_ID);
    }

    /**
     * Perform actions after object load
     *
     * @param AbstractModel|OrderPickupLocationInterface $object
     * @return AbstractDb
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_string($street)) {
            $exploded = explode("\n", $street);
            $object->setData(OrderPickupLocationInterface::STREET, $exploded);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel|OrderPickupLocationInterface $object
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_array($street)) {
            $imploded = implode("\n", $street);
            $object->setData(OrderPickupLocationInterface::STREET, $imploded);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|OrderPickupLocationInterface $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_string($street)) {
            $exploded = explode("\n", $street);
            $object->setData(OrderPickupLocationInterface::STREET, $exploded);
        }

        return parent::_afterSave($object);
    }
}
