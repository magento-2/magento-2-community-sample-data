<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Shipment;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Shipment Resource Model
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentReference extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Shipment constructor.
     * @param Context $context
     * @param EntityManager $entityManager
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Init main table and primary key
     * @return void
     */
    protected function _construct()
    {
         $this->_init(SetupSchema::TABLE_SHIPMENT, ShipmentReferenceInterface::ENTITY_ID);
    }

    /**
     * @param AbstractModel $object
     * @param int $value
     * @param null $field
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @param int $shipmentId
     * @return string
     */
    public function getIdByShipmentId($shipmentId)
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();
        $table      = $this->getTable($tableName);

        $select = $connection->select()
            ->from($table, ShipmentReferenceInterface::ENTITY_ID)
            ->where('shipment_id = :shipment_id');

        $bind  = [':shipment_id' => (string)$shipmentId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * @param int $shipmentId
     *
     * @return string
     */
    public function getIdByExtShipmentId($shipmentId)
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();
        $table      = $this->getTable($tableName);

        $select = $connection->select()
            ->from($table, ShipmentReferenceInterface::ENTITY_ID)
            ->where('ext_shipment_id = :ext_shipment_id');

        $bind = [':ext_shipment_id' => (string)$shipmentId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * @param string[] $extShipmentIds
     *
     * @return string[]
     */
    public function getShipmentIdsByExtShipmentIds(array $extShipmentIds)
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();
        $table      = $this->getTable($tableName);

        $bind = [];
        foreach ($extShipmentIds as $index => $extShipmentId) {
            $bind[":id{$index}"] = $extShipmentId;
        }

        $select = $connection->select()
            ->from($table, [ShipmentReferenceInterface::EXT_SHIPMENT_ID, ShipmentReferenceInterface::SHIPMENT_ID])
            ->where('ext_shipment_id in (' . implode(',', array_keys($bind)) . ')');

        return $connection->fetchPairs($select, $bind);
    }
}
