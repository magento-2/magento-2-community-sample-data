<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Klarna\Core\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        if ($installer->tableExists('klarna_core_order')) {
            return;
        }

        $installer->startSetup();

        /**
         * Create table 'klarna_kco_order'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('klarna_core_order'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true,
                ],
                'Entity Id'
            )
            ->addColumn(
                'klarna_order_id',
                Table::TYPE_TEXT,
                255,
                [],
                'Klarna Order Id'
            )
            ->addColumn(
                'session_id',
                Table::TYPE_TEXT,
                255,
                [],
                'Session Id'
            )
            ->addColumn(
                'reservation_id',
                Table::TYPE_TEXT,
                255,
                [],
                'Reservation Id'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Order Id'
            )
            ->addColumn(
                'is_acknowledged',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'default'  => '0',
                ],
                'Is Acknowledged'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'klarna_core_order',
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            )
            ->setComment('Klarna Order');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
