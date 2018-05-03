<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Uninstall
 *
 * @package  Temando\Shipping\Setup
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Uninstall implements UninstallInterface
{
    /**
     * Remove data that was created during module installation.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $uninstaller = $setup;

        $configTable = $uninstaller->getTable('core_config_data');

        $uninstaller->getConnection()->dropTable(SetupSchema::TABLE_SHIPMENT);
        $uninstaller->getConnection()->dropTable(SetupSchema::TABLE_ORDER);
        $uninstaller->getConnection()->dropTable(SetupSchema::TABLE_CHECKOUT_ADDRESS);
        $uninstaller->getConnection()->dropTable(RmaSetupSchema::TABLE_RMA_SHIPMENT);
        $uninstaller->getConnection()->delete($configTable, "`path` LIKE 'carriers/temando/%'");
    }
}
