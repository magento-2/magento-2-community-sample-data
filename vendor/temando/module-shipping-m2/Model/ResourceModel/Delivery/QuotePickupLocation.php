<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\Delivery;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Quote Pickup Location Resource Model
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class QuotePickupLocation extends AbstractDb
{
    /**
     * Serializable fields declaration
     * - serialized: JSON object
     * - unserialized: associative array
     *
     * @var mixed[]
     */
    protected $_serializableFields = [
        QuotePickupLocationInterface::OPENING_HOURS => [
            [],
            [],
        ],
        QuotePickupLocationInterface::SHIPPING_EXPERIENCES => [
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
        $this->_init(SetupSchema::TABLE_QUOTE_PICKUP_LOCATION, QuotePickupLocationInterface::ENTITY_ID);
    }

    /**
     * @param AbstractModel|QuotePickupLocationInterface $object
     * @param int $value
     * @param null $field
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        parent::load($object, $value);

        $street = $object->getStreet();
        if (is_string($street)) {
            $exploded = explode("\n", $street);
            $object->setData(QuotePickupLocationInterface::STREET, $exploded);
        }

        // as of v1.3.0 the serialized data structure changed
        $openingHours = $object->getOpeningHours();
        if (!array_key_exists('general', $openingHours)) {
            $openingHours['general'] = $openingHours;
            $openingHours['specific'] = [];
            $object->setData(QuotePickupLocationInterface::OPENING_HOURS, $openingHours);
        }

        $object->setData(QuotePickupLocationInterface::SELECTED, (bool) $object->isSelected());

        return $this;
    }

    /**
     * @param AbstractModel|QuotePickupLocationInterface $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $street = $object->getStreet();
        if (is_array($street)) {
            $imploded = implode("\n", $street);
            $object->setData(QuotePickupLocationInterface::STREET, $imploded);
        }

        parent::save($object);

        if (is_string($street)) {
            $exploded = explode("\n", $street);
            $object->setData(QuotePickupLocationInterface::STREET, $exploded);
        }

        return $this;
    }
}
