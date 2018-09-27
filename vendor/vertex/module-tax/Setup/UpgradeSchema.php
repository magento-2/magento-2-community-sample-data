<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Module\Setup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Schema Upgrade Script
 *
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $db = $installer->getConnection();
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $installer->getConnection()->changeColumn(
                $installer->getTable('vertex_taxrequest'),
                'quote_id',
                'quote_id',
                [
                    'type' => Table::TYPE_BIGINT,
                    'length' => 20,
                ]
            );

            $installer->getConnection()->changeColumn(
                $installer->getTable('vertex_taxrequest'),
                'order_id',
                'order_id',
                [
                    'type' => Table::TYPE_BIGINT,
                    'length' => 20,
                ]
            );
        }

        if (version_compare($context->getVersion(), '100.0.1') < 0) {
            $table = $installer->getTable('vertex_taxrequest');
            $db->changeColumn(
                $table,
                'request_id',
                'request_id',
                [
                    'type' => Table::TYPE_BIGINT,
                    'length' => 20,
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true,
                ]
            );
        }

        if (version_compare($context->getVersion(), '100.1.0') < 0) {
            $this->createCustomerCodeTable($setup);
            $this->dropTaxAreaIdColumns($setup);
            $this->createVertexInvoiceSentTable($setup);
            $this->migrateInvoiceSentData($setup);
            $this->deleteInvoiceSentColumnFromInvoiceTable($setup);
        }

        if (version_compare($context->getVersion(), '100.2.0') < 0) {
            $this->createOrderInvoiceStatusTable($setup);
        }
    }

    /**
     * Create the Vertex Customer Code table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function createCustomerCodeTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('vertex_customer_code');

        $table = $setup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'primary' => true,
                    'nullable' => false,
                    'unsigned' => true
                ],
                'Customer ID'
            )
            ->addColumn(
                'customer_code',
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true,
                ],
                'Customer Code for Vertex'
            );

        $setup->getConnection()
            ->createTable($table);
    }

    /**
     * Drop Tax Area IDs from the Address Tables
     *
     * @param SchemaSetupInterface $setup
     */
    private function dropTaxAreaIdColumns(SchemaSetupInterface $setup)
    {
        $orderTable = $setup->getTable('sales_order_address');
        if ($this->getConnection($setup, 'sales')->tableColumnExists($orderTable, 'tax_area_id')) {
            $this->getConnection($setup, 'sales')->dropColumn($orderTable, 'tax_area_id');
        }

        $quoteTable = $setup->getTable('quote_address');
        if ($this->getConnection($setup, 'checkout')->tableColumnExists($quoteTable, 'tax_area_id')) {
            $this->getConnection($setup, 'checkout')->dropColumn($quoteTable, 'tax_area_id');
        }
    }

    /**
     * Create the Vertex Invoice Sent table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function createVertexInvoiceSentTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('vertex_invoice_sent');

        $table = $setup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'invoice_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'primary' => true,
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Invoice ID'
            )
            ->addColumn(
                'sent_to_vertex',
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'default' => 0,
                ],
                'Invoice has been logged in Vertex'
            );

        $setup->getConnection()
            ->createTable($table);
    }

    /**
     * Migrate Invoice Sent data from the old table column to the new table
     *
     * @param SchemaSetupInterface $setup
     */
    private function migrateInvoiceSentData(SchemaSetupInterface $setup)
    {
        $salesDb = $this->getConnection($setup, 'sales');
        $db = $setup->getConnection();
        $oldTableName = $setup->getTable('sales_invoice');
        $newTableName = $setup->getTable('vertex_invoice_sent');

        if (!$salesDb->tableColumnExists($oldTableName, 'vertex_invoice_sent')) {
            return;
        }

        $select = $salesDb->select()
            ->from($oldTableName)
            ->where('vertex_invoice_sent = 1');

        $results = array_map(
            function ($rawResult) {
                return [
                    'invoice_id' => $rawResult['entity_id'],
                    'sent_to_vertex' => 1,
                ];
            },
            $salesDb->fetchAll($select)
        );

        if (!count($results)) {
            return;
        }

        $db->insertMultiple(
            $newTableName,
            $results
        );
    }

    /**
     * Delete the old Invoice Sent column from the Invoice table
     *
     * @param SchemaSetupInterface $setup
     */
    private function deleteInvoiceSentColumnFromInvoiceTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('sales_invoice');
        if ($this->getConnection($setup, 'sales')->tableColumnExists($table, 'vertex_invoice_sent')) {
            $this->getConnection($setup, 'sales')->dropColumn($table, 'vertex_invoice_sent');
        }
    }

    /**
     * Create a table holding a boolean flag for whether or not a Vertex Invoice has been sent for the order
     *
     * @param SchemaSetupInterface $setup
     */
    private function createOrderInvoiceStatusTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('vertex_order_invoice_status');

        $table = $setup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'primary' => true,
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Order ID'
            )
            ->addColumn(
                'sent_to_vertex',
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'default' => 0,
                ],
                'Invoice has been logged in Vertex'
            );

        $setup->getConnection()
            ->createTable($table);
    }

    /**
     * Retrieve Connection
     *
     * @param SchemaSetupInterface $setup
     * @param string $connectionName
     * @return AdapterInterface
     */
    private function getConnection(SchemaSetupInterface $setup, $connectionName)
    {
        if ($setup instanceof Setup) {
            return $setup->getConnection($connectionName);
        }
        return $setup->getConnection();
    }
}
