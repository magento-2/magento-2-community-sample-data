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
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 *
 * @package Klarna\Core\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<') && $installer->tableExists('klarna_kco_order')) {
            $oldTable = $installer->getTable('klarna_kco_order');
            $newTable = $installer->getTable('klarna_core_order');

            $installer->getConnection()->renameTable($oldTable, $newTable);
            $installer->getConnection()
                ->addColumn(
                    $newTable,
                    'session_id',
                    [
                        'type'    => Table::TYPE_TEXT,
                        'length'  => 255,
                        'comment' => 'Session Id',
                        'after'   => 'klarna_checkout_id'
                    ]
                );
            $installer->getConnection()
                ->changeColumn(
                    $newTable,
                    'kco_order_id',
                    'id',
                    [
                        'type'     => Table::TYPE_INTEGER,
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'comment'  => 'Entity Id'
                    ]
                );
            $installer->getConnection()
                ->changeColumn(
                    $newTable,
                    'klarna_checkout_id',
                    'klarna_order_id',
                    [
                        'type'    => Table::TYPE_TEXT,
                        'length'  => 255,
                        'comment' => 'Klarna Order Id'
                    ]
                );
            $installer->getConnection()
                ->changeColumn(
                    $newTable,
                    'klarna_reservation_id',
                    'reservation_id',
                    [
                        'type'    => Table::TYPE_TEXT,
                        'length'  => 255,
                        'comment' => 'Reservation Id'
                    ]
                );
        }
        $installer->endSetup();
    }
}
