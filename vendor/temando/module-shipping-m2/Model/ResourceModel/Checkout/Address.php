<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Checkout;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Serialize\SerializerInterface;
use Temando\Shipping\Api\Data\Checkout\AddressInterface;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Checkout shipping address extension resource model
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Address extends AbstractDb
{
    /**
     * Serializable fields declaration
     * - serialized: JSON object
     * - unserialized: associative array
     *
     * @var mixed[]
     */
    protected $_serializableFields = [
        AddressInterface::SERVICE_SELECTION => [
            [],
            [],
        ],
    ];

    /**
     * Address constructor.
     * @param Snapshot $entitySnapshot
     * @param RelationComposite $entityRelationComposite
     * @param $context
     * @param SerializerInterface $serializer
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        SerializerInterface $serializer,
        $connectionName = null
    ) {
        $this->serializer = $serializer;

        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
    }

    /**
     * Init main table and primary key
     * @return void
     */
    protected function _construct()
    {
         $this->_init(SetupSchema::TABLE_CHECKOUT_ADDRESS, AddressInterface::ENTITY_ID);
    }

    /**
     * @param int $quoteAddressId
     * @return string
     */
    public function getIdByQuoteAddressId($quoteAddressId)
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();
        $table      = $this->getTable($tableName);

        $select = $connection->select()
            ->from($table, AddressInterface::ENTITY_ID)
            ->where('shipping_address_id = :shipping_address_id');

        $bind  = [':shipping_address_id' => (string)$quoteAddressId];

        return $connection->fetchOne($select, $bind);
    }
}
