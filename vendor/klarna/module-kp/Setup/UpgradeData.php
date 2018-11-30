<?php
/**
 * This file is part of the Klarna Kp module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Upgrades DB data for a module
     *
     * @param ModuleDataSetupInterface $installer
     * @param ModuleContextInterface   $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        if (version_compare($context->getVersion(), '4.0.2', '<')) {
            $table = $installer->getTable('klarna_payments_quote');
            // Mark all quotes as inactive so that switch over to new payments endpoint happens
            $installer->getConnection()->update($table, ['is_active' => 0]);
        }
        if (version_compare($context->getVersion(), '5.3.2', '<')) {
            $methods = [
                'klarna_pay_later',
                'klarna_pay_now',
                'klarna_pay_over_time',
                'klarna_direct_debit',
                'klarna_direct_bank_transfer'
            ];
            $methods = "'" . implode("','", $methods) . "'";
            $installer->getConnection()->update(
                $installer->getTable('klarna_payments_quote'),
                ['is_active' => 0, 'payment_method_info' => '{}'],
                '`payment_method_info` is null'
            );
            $installer->getConnection()
                ->query("update `{$installer->getTable('sales_order_payment')}`" .
                    " set `additional_information`=" .
                    " replace(`additional_information`, '}', concat(',\"method_code\":\"', `method`, '\"}'))" .
                    " where `method` in ({$methods})");
            $installer->getConnection()
                ->update(
                    $installer->getTable('sales_order_payment'),
                    ['method' => 'klarna_kp'],
                    "`method` in ({$methods})"
                );
            foreach (['sales_order_grid', 'sales_invoice_grid', 'sales_creditmemo_grid'] as $table) {
                $installer->getConnection()
                    ->update(
                        $installer->getTable($table),
                        ['payment_method' => 'klarna_kp'],
                        "`payment_method` in ({$methods})"
                    );
            }
        }
        if (version_compare($context->getVersion(), '5.4.5', '<')) {
            $values = [
                '<strong>',
                '<\/strong>'
            ];
            foreach ($values as $value) {
                $manipulation = new \Zend_Db_Expr("replace(`additional_information`, '$value', '')");
                $installer->getConnection()
                    ->update(
                        $installer->getTable('sales_order_payment'),
                        ['additional_information' => $manipulation],
                        "`method` = 'klarna_kp'"
                    );
            }
        }
        $installer->endSetup();
    }
}
