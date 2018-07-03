<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\CollectionPoint;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Quote Collection Point Resource Model
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class QuoteCollectionPoint extends AbstractDb
{
    /**
     * Serializable fields declaration
     * - serialized: JSON object
     * - unserialized: associative array
     *
     * @var mixed[]
     */
    protected $_serializableFields = [
        QuoteCollectionPointInterface::OPENING_HOURS => [
            [],
            [],
        ],
        QuoteCollectionPointInterface::SHIPPING_EXPERIENCES => [
            [],
            [],
        ]
    ];

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SetupSchema::TABLE_QUOTE_COLLECTION_POINT, QuoteCollectionPointInterface::ENTITY_ID);
    }

    /**
     * Perform actions after object load
     *
     * @param AbstractModel|QuoteCollectionPointInterface $object
     * @return AbstractDb
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_string($street)) {
            $exploded = explode("\n", $street);
            $object->setData(QuoteCollectionPointInterface::STREET, $exploded);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel|QuoteCollectionPointInterface $object
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_array($street)) {
            $imploded = implode("\n", $street);
            $object->setData(QuoteCollectionPointInterface::STREET, $imploded);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|QuoteCollectionPointInterface $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_string($street)) {
            $exploded = explode("\n", $street);
            $object->setData(QuoteCollectionPointInterface::STREET, $exploded);
        }

        return parent::_afterSave($object);
    }
}
