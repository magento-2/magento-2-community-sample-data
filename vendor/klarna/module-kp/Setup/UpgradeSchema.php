<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

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

        if (version_compare($context->getVersion(), '4.0.3', '<')) {
            $table = $installer->getTable('klarna_payments_quote');

            $ddl = $installer->getConnection()->describeTable($table);
            if (!isset($ddl['payment_methods'])) {
                $installer->getConnection()
                    ->addColumn(
                        $table,
                        'payment_methods',
                        [
                            'type'    => Table::TYPE_TEXT,
                            'length'  => 255,
                            'comment' => 'Payment Method Categories'
                        ]
                    );
            }
        }
        if (version_compare($context->getVersion(), '5.3.1', '<')) {
            $table = $installer->getTable('klarna_payments_quote');

            $ddl = $installer->getConnection()->describeTable($table);
            if (!isset($ddl['payment_method_info'])) {
                $installer->getConnection()
                    ->addColumn(
                        $table,
                        'payment_method_info',
                        [
                            'type'    => Table::TYPE_TEXT,
                            'length'  => 4096,
                            'comment' => 'Payment Method Category Info'
                        ]
                    );
            }
        }        $installer->endSetup();
    }
}
