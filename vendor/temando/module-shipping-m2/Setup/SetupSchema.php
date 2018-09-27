<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Temando\Shipping\Api\Data\Checkout\AddressInterface;
use Temando\Shipping\Api\Data\CollectionPoint\OrderCollectionPointInterface;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface;
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
    const CHECKOUT_CONNECTION_NAME = 'checkout';
    const SALES_CONNECTION_NAME = 'sales';

    const TABLE_SHIPMENT = 'temando_shipment';
    const TABLE_ORDER = 'temando_order';
    const TABLE_CHECKOUT_ADDRESS = 'temando_checkout_address';
    const TABLE_COLLECTION_POINT_SEARCH = 'temando_collection_point_search';
    const TABLE_QUOTE_COLLECTION_POINT = 'temando_quote_collection_point';
    const TABLE_ORDER_COLLECTION_POINT = 'temando_order_collection_point';

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createShipmentTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection(self::SALES_CONNECTION_NAME)->newTable(
            $installer->getTable(self::TABLE_SHIPMENT, self::SALES_CONNECTION_NAME)
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
            $installer->getTable('sales_shipment', self::SALES_CONNECTION_NAME),
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

        $installer->getConnection(self::SALES_CONNECTION_NAME)->createTable($table);
    }

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createOrderTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection(self::SALES_CONNECTION_NAME)->newTable(
            $installer->getTable(self::TABLE_ORDER, self::SALES_CONNECTION_NAME)
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
            $installer->getTable('sales_order', self::SALES_CONNECTION_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $table->setComment(
            'Temando Order'
        );

        $installer->getConnection(self::SALES_CONNECTION_NAME)->createTable($table);
    }

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createAddressTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->newTable(
            $installer->getTable(self::TABLE_CHECKOUT_ADDRESS, self::CHECKOUT_CONNECTION_NAME)
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
            $installer->getTable('quote_address', self::CHECKOUT_CONNECTION_NAME),
            'address_id',
            Table::ACTION_CASCADE
        );

        $table->setComment(
            'Temando Checkout Address'
        );

        $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->createTable($table);
    }

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $installer
     *
     * @return void
     */
    public function setShipmentOriginLocationNullable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(self::TABLE_SHIPMENT, self::SALES_CONNECTION_NAME);
        $installer->getConnection(self::SALES_CONNECTION_NAME)->modifyColumn(
            $tableName,
            ShipmentReferenceInterface::EXT_LOCATION_ID,
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 64,
                'nullable' => true,
            ]
        );
    }

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createCollectionPointSearchTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->newTable(
            $installer->getTable(self::TABLE_COLLECTION_POINT_SEARCH, self::CHECKOUT_CONNECTION_NAME)
        );

        $table->addColumn(
            SearchRequestInterface::SHIPPING_ADDRESS_ID,
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table->addColumn(
            SearchRequestInterface::COUNTRY_ID,
            Table::TYPE_TEXT,
            2,
            ['nullable' => false],
            'Country Code'
        );

        $table->addColumn(
            SearchRequestInterface::POSTCODE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Zip/Postal Code'
        );

        $table->addForeignKey(
            $installer->getFkName(
                self::TABLE_COLLECTION_POINT_SEARCH,
                SearchRequestInterface::SHIPPING_ADDRESS_ID,
                'quote_address',
                'address_id'
            ),
            SearchRequestInterface::SHIPPING_ADDRESS_ID,
            $installer->getTable('quote_address', self::CHECKOUT_CONNECTION_NAME),
            'address_id',
            Table::ACTION_CASCADE
        );

        $countryTable = $installer->getTable('directory_country', self::CHECKOUT_CONNECTION_NAME);
        if ($installer->tableExists($countryTable, self::CHECKOUT_CONNECTION_NAME)) {
            $table->addForeignKey(
                $installer->getFkName(
                    self::TABLE_COLLECTION_POINT_SEARCH,
                    SearchRequestInterface::COUNTRY_ID,
                    'directory_country',
                    'country_id'
                ),
                SearchRequestInterface::COUNTRY_ID,
                $countryTable,
                'country_id',
                Table::ACTION_NO_ACTION
            );
        }

        $table->setComment('Collection Point Search');

        $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->createTable($table);
    }

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createQuoteCollectionPointTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->newTable(
            $installer->getTable(self::TABLE_QUOTE_COLLECTION_POINT, self::CHECKOUT_CONNECTION_NAME)
        );

        $table->addColumn(
            QuoteCollectionPointInterface::ENTITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Quote Address Id'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::COLLECTION_POINT_ID,
            Table::TYPE_TEXT,
            64,
            ['nullable' => false],
            'Collection Point Id'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::NAME,
            Table::TYPE_TEXT,
            255,
            [],
            'Name'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::COUNTRY,
            Table::TYPE_TEXT,
            2,
            ['nullable' => false],
            'Country Code'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::REGION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Region'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::POSTCODE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Zip/Postal Code'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::CITY,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'City'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::STREET,
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Street'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::OPENING_HOURS,
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Opening Hours'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::SHIPPING_EXPERIENCES,
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Shipping Experiences'
        );

        $table->addColumn(
            QuoteCollectionPointInterface::SELECTED,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Selected'
        );

        $table->addForeignKey(
            $installer->getFkName(
                self::TABLE_QUOTE_COLLECTION_POINT,
                QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID,
                self::TABLE_COLLECTION_POINT_SEARCH,
                SearchRequestInterface::SHIPPING_ADDRESS_ID
            ),
            QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID,
            $installer->getTable(self::TABLE_COLLECTION_POINT_SEARCH, self::CHECKOUT_CONNECTION_NAME),
            SearchRequestInterface::SHIPPING_ADDRESS_ID,
            Table::ACTION_CASCADE
        );

        $table->setComment('Quote Collection Point Entity');

        $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->createTable($table);
    }

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createOrderCollectionPointTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection(self::SALES_CONNECTION_NAME)->newTable(
            $installer->getTable(self::TABLE_ORDER_COLLECTION_POINT, self::SALES_CONNECTION_NAME)
        );

        $table->addColumn(
            OrderCollectionPointInterface::RECIPIENT_ADDRESS_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table->addColumn(
            OrderCollectionPointInterface::COLLECTION_POINT_ID,
            Table::TYPE_TEXT,
            64,
            ['nullable' => false],
            'Collection Point Id'
        );

        $table->addColumn(
            OrderCollectionPointInterface::NAME,
            Table::TYPE_TEXT,
            255,
            [],
            'Name'
        );

        $table->addColumn(
            OrderCollectionPointInterface::COUNTRY,
            Table::TYPE_TEXT,
            2,
            ['nullable' => false],
            'Country Code'
        );

        $table->addColumn(
            OrderCollectionPointInterface::REGION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Region'
        );

        $table->addColumn(
            OrderCollectionPointInterface::POSTCODE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Zip/Postal Code'
        );

        $table->addColumn(
            OrderCollectionPointInterface::CITY,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'City'
        );

        $table->addColumn(
            OrderCollectionPointInterface::STREET,
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Street'
        );

        $table->addForeignKey(
            $installer->getFkName(
                self::TABLE_QUOTE_COLLECTION_POINT,
                OrderCollectionPointInterface::RECIPIENT_ADDRESS_ID,
                'sales_order_address',
                'entity_id'
            ),
            OrderCollectionPointInterface::RECIPIENT_ADDRESS_ID,
            $installer->getTable('sales_order_address', self::SALES_CONNECTION_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $table->setComment('Order Collection Point Entity');

        $installer->getConnection(self::SALES_CONNECTION_NAME)->createTable($table);
    }

    /**
     * Add an indicator for collection point checkout being in progress.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $installer
     * @return void
     */
    public function addCollectionPointSearchPendingColumn(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(self::TABLE_COLLECTION_POINT_SEARCH, self::CHECKOUT_CONNECTION_NAME);

        // allow empty values for pending searches
        $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->modifyColumn(
            $tableName,
            SearchRequestInterface::COUNTRY_ID,
            ['type' => Table::TYPE_TEXT, 'length' => 2, 'nullable' => true]
        );
        $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->modifyColumn(
            $tableName,
            SearchRequestInterface::POSTCODE,
            ['type' => Table::TYPE_TEXT, 'length' => 255, 'nullable' => true]
        );

        // add pending indicator
        $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->addColumn(
            $tableName,
            SearchRequestInterface::PENDING,
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => 0,
                'comment'  => 'Pending'
            ]
        );
    }
}
