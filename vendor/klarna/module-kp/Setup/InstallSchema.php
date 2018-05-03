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
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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

        $installer->startSetup();

        /**
         * Create table 'klarna_payments_quote'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('klarna_payments_quote'))
            ->addColumn(
                'payments_quote_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true,
                ],
                'Payments Id'
            )
            ->addColumn(
                'session_id',
                Table::TYPE_TEXT,
                255,
                [],
                'Klarna Session Id'
            )
            ->addColumn(
                'client_token',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Klarna Client Token'
            )
            ->addColumn(
                'authorization_token',
                Table::TYPE_TEXT,
                255,
                [],
                'Authorization Token'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'default'  => '0',
                ],
                'Is Active'
            )
            ->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Quote Id'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'klarna_payments_quote',
                    'quote_id',
                    'quote',
                    'entity_id'
                ),
                'quote_id',
                $installer->getTable('quote'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Klarna Payments Quote');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
