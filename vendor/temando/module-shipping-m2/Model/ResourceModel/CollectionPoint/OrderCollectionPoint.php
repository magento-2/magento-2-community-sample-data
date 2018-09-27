<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\CollectionPoint;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Temando\Shipping\Api\Data\CollectionPoint\OrderCollectionPointInterface;
use Temando\Shipping\Model\ResourceModel\Db\NoSequenceDb;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Order Collection Point Resource Model
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderCollectionPoint extends NoSequenceDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SetupSchema::TABLE_ORDER_COLLECTION_POINT, OrderCollectionPointInterface::RECIPIENT_ADDRESS_ID);
    }

    /**
     * Perform actions after object load
     *
     * @param AbstractModel|OrderCollectionPointInterface $object
     * @return AbstractDb
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_string($street)) {
            $exploded = explode("\n", $street);
            $object->setData(OrderCollectionPointInterface::STREET, $exploded);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel|OrderCollectionPointInterface $object
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_array($street)) {
            $imploded = implode("\n", $street);
            $object->setData(OrderCollectionPointInterface::STREET, $imploded);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|OrderCollectionPointInterface $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_string($street)) {
            $exploded = explode("\n", $street);
            $object->setData(OrderCollectionPointInterface::STREET, $exploded);
        }

        return parent::_afterSave($object);
    }
}
