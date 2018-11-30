<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Order Reference Resource Model
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderReference extends AbstractDb
{
    /**
     * Init main table and primary key
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SetupSchema::TABLE_ORDER, OrderReferenceInterface::ENTITY_ID);
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getIdByOrderId($orderId)
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();
        $table      = $this->getTable($tableName);

        $select = $connection->select()
            ->from($table, OrderReferenceInterface::ENTITY_ID)
            ->where('order_id = :order_id');

        $bind  = [':order_id' => (string)$orderId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * @param string $extOrderId
     * @return string
     */
    public function getIdByExtOrderId($extOrderId)
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();
        $table      = $this->getTable($tableName);

        $select = $connection->select()
            ->from($table, OrderReferenceInterface::ENTITY_ID)
            ->where('ext_order_id = :ext_order_id');

        $bind  = [':ext_order_id' => (string)$extOrderId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * @param string $extOrderId
     * @return string
     */
    public function getOrderIdByExtOrderId($extOrderId)
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();
        $table      = $this->getTable($tableName);

        $select = $connection->select()
            ->from($table, OrderReferenceInterface::ORDER_ID)
            ->where('ext_order_id = :ext_order_id');

        $bind  = [':ext_order_id' => (string)$extOrderId];

        return $connection->fetchOne($select, $bind);
    }
}
