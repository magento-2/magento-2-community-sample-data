<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Batch;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Batch Order Collection
 *
 * @package Temando\Shipping\Model
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderCollection extends Collection
{
    /**
     * @param string $carrierCode
     */
    public function addCarrierFilter($carrierCode)
    {
        $this->addFieldToFilter('shipping_method', ['like' => ["$carrierCode%"]]);
    }

    /**
     * Filter orders which can be shipped. This is not complete. Filtering
     * remaining items in a loop is still necessary.
     * @see \Magento\Sales\Model\Order::canShip()
     *
     * @link https://magento.stackexchange.com/a/72600
     *
     * @return void
     */
    public function addCanShipFilter()
    {
        $orderItemTable = $this->getTable('sales_order_item');
        $pickupLocationTable = $this->getTable(SetupSchema::TABLE_ORDER_PICKUP_LOCATION);

        $select = $this->getSelect();
        $select->join(
            ['order_item' => $orderItemTable],
            'main_table.entity_id = order_item.order_id',
            []
        );
        $select->joinLeft(
            ['pickup_location' => $pickupLocationTable],
            'main_table.shipping_address_id = pickup_location.recipient_address_id',
            []
        );

        $this->addFieldToFilter(sprintf('main_table.%s', OrderInterface::IS_VIRTUAL), 0);
        $this->addFieldToFilter(OrderInterface::STATE, ['nin' => [
            \Magento\Sales\Model\Order::STATE_HOLDED,
            \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW,
            \Magento\Sales\Model\Order::STATE_CANCELED,
        ]]);

        $this->addExpressionFieldToSelect(
            'qty_to_ship',
            'FLOOR(SUM({{qty_ordered}}) - SUM({{qty_shipped}}))',
            [
                'qty_ordered' => 'order_item.qty_ordered',
                'qty_shipped' => 'order_item.qty_shipped',
            ]
        );
        $this->addExpressionFieldToSelect(
            'locked',
            'SUM(COALESCE(locked_do_ship, 0))',
            [
                'qty_ordered' => 'order_item.qty_ordered',
                'qty_shipped' => 'order_item.qty_shipped',
            ]
        );

        $select->group(['main_table.entity_id']);
        $select->having('qty_to_ship > 0 AND locked = 0');
        $select->where('pickup_location.pickup_location_id IS NULL');
    }

    /**
     * Retain GROUP BY, use sub-select for counting.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $select = clone $this->getSelect();
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $countSelect = $this->_conn->select();
        $countSelect->from(['s' => $select]);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));

        return $countSelect;
    }
}
