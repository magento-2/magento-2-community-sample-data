<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ResourceConnection;
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
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Uninstall constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Remove data that was created during module installation.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $uninstaller = $setup;

        $defaultConnection = $uninstaller->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $checkoutConnection = $uninstaller->getConnection(SetupSchema::CHECKOUT_CONNECTION_NAME);
        $salesConnection = $uninstaller->getConnection(SetupSchema::SALES_CONNECTION_NAME);

        $salesConnection->dropTable(SetupSchema::TABLE_ORDER);
        $checkoutConnection->dropTable(SetupSchema::TABLE_QUOTE_COLLECTION_POINT);
        $checkoutConnection->dropTable(SetupSchema::TABLE_COLLECTION_POINT_SEARCH);
        $checkoutConnection->dropTable(SetupSchema::TABLE_ORDER_COLLECTION_POINT);
        $checkoutConnection->dropTable(SetupSchema::TABLE_CHECKOUT_ADDRESS);
        $salesConnection->dropTable(SetupSchema::TABLE_SHIPMENT);
        $defaultConnection->dropTable(RmaSetupSchema::TABLE_RMA_SHIPMENT);

        $configTable = $uninstaller->getTable('core_config_data');
        $defaultConnection->delete($configTable, "`path` LIKE 'carriers/temando/%'");

        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(Product::ENTITY, SetupData::ATTRIBUTE_CODE_HEIGHT);
        $eavSetup->removeAttribute(Product::ENTITY, SetupData::ATTRIBUTE_CODE_WIDTH);
        $eavSetup->removeAttribute(Product::ENTITY, SetupData::ATTRIBUTE_CODE_LENGTH);
    }
}
