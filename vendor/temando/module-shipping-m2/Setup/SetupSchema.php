<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Temando\Shipping\Api\Data\Checkout\AddressInterface;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;

/**
 * Schema setup for use during installation / upgrade
 *
 * @package  Temando\Shipping\Setup
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class SetupSchema
{
    const TABLE_SHIPMENT         = 'temando_shipment';
    const TABLE_ORDER            = 'temando_order';
    const TABLE_CHECKOUT_ADDRESS = 'temando_checkout_address';

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createShipmentTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(self::TABLE_SHIPMENT)
        );

        $table->addColumn(
            ShipmentReferenceInterface::ENTITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table->addColumn(
            ShipmentReferenceInterface::SHIPMENT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Magento Shipment Id'
        );

        $table->addColumn(
            ShipmentReferenceInterface::EXT_SHIPMENT_ID,
            Table::TYPE_TEXT,
            64,
            ['nullable' => false],
            'External Shipment Id'
        );

        $table->addColumn(
            ShipmentReferenceInterface::EXT_LOCATION_ID,
            Table::TYPE_TEXT,
            64,
            ['nullable' => false],
            'External Location Id'
        );

        $table->addColumn(
            ShipmentReferenceInterface::EXT_TRACKING_URL,
            Table::TYPE_TEXT,
            255,
            [],
            'External Tracking Url'
        );

        $table->addColumn(
            ShipmentReferenceInterface::EXT_TRACKING_REFERENCE,
            Table::TYPE_TEXT,
            255,
            [],
            'External Tracking Reference'
        );

        $table->addForeignKey(
            $installer->getFkName(
                self::TABLE_SHIPMENT,
                ShipmentReferenceInterface::SHIPMENT_ID,
                'sales_shipment',
                'entity_id'
            ),
            ShipmentReferenceInterface::SHIPMENT_ID,
            $installer->getTable('sales_shipment'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $table->addIndex(
            $installer->getIdxName(
                self::TABLE_SHIPMENT,
                [ShipmentReferenceInterface::SHIPMENT_ID, ShipmentReferenceInterface::EXT_SHIPMENT_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [ShipmentReferenceInterface::SHIPMENT_ID, ShipmentReferenceInterface::EXT_SHIPMENT_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );

        $table->setComment(
            'Temando Shipment'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createOrderTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(self::TABLE_ORDER)
        );

        $table->addColumn(
            OrderReferenceInterface::ENTITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table->addColumn(
            OrderReferenceInterface::ORDER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Magento Order Id'
        );

        $table->addColumn(
            OrderReferenceInterface::EXT_ORDER_ID,
            Table::TYPE_TEXT,
            64,
            ['nullable' => false],
            'Temando Order Id'
        );

        $table->addForeignKey(
            $installer->getFkName(
                self::TABLE_ORDER,
                OrderReferenceInterface::ORDER_ID,
                'sales_order',
                'entity_id'
            ),
            OrderReferenceInterface::ORDER_ID,
            $installer->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $table->setComment(
            'Temando Order'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createAddressTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(self::TABLE_CHECKOUT_ADDRESS)
        );

        $table->addColumn(
            AddressInterface::ENTITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table->addColumn(
            AddressInterface::SHIPPING_ADDRESS_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Magento Quote Address Id'
        );

        $table->addColumn(
            AddressInterface::SERVICE_SELECTION,
            Table::TYPE_TEXT,
            null,
            [],
            'Value Added Services'
        );

        $table->addForeignKey(
            $installer->getFkName(
                self::TABLE_CHECKOUT_ADDRESS,
                AddressInterface::SHIPPING_ADDRESS_ID,
                'quote_address',
                'address_id'
            ),
            AddressInterface::SHIPPING_ADDRESS_ID,
            $installer->getTable('quote_address'),
            'address_id',
            Table::ACTION_CASCADE
        );

        $table->setComment(
            'Temando Checkout Address'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    public function setShipmentOriginLocationNullable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(self::TABLE_SHIPMENT);
        $installer->getConnection()->modifyColumn(
            $tableName,
            ShipmentReferenceInterface::EXT_LOCATION_ID,
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 64,
                'nullable' => true,
            ]
        );
    }
}
