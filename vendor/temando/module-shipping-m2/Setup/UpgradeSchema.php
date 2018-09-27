<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Init module schema
 *
 * @package  Temando\Shipping\Setup
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var SetupSchema
     */
    private $installer;

    /**
     * @var RmaSetupSchema
     */
    private $rmaInstaller;

    /**
     * UpgradeSchema constructor.
     *
     * @param SetupSchema $installer
     * @param RmaSetupSchema $rmaInstaller
     */
    public function __construct(SetupSchema $installer, RmaSetupSchema $rmaInstaller)
    {
        $this->installer = $installer;
        $this->rmaInstaller = $rmaInstaller;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // beware, this is the version we are upgrading from, not to!
        $moduleVersion = $context->getVersion();

        if (version_compare($moduleVersion, '0.1.0', '<')) {
            $this->installer->createOrderTable($setup);
            $this->installer->createShipmentTable($setup);
        }

        if (version_compare($moduleVersion, '0.2.6', '<')) {
            $this->installer->setShipmentOriginLocationNullable($setup);
        }

        if (version_compare($moduleVersion, '0.3.1', '<')) {
            $this->installer->createAddressTable($setup);
        }

        if (version_compare($moduleVersion, '1.1.0', '<')) {
            $this->rmaInstaller->createRmaShipmentTable($setup);
        }

        if (version_compare($moduleVersion, '1.2.0', '<')) {
            $this->rmaInstaller->addReturnShipmentIdColumn($setup);
            $this->installer->createCollectionPointSearchTable($setup);
            $this->installer->createQuoteCollectionPointTable($setup);
            $this->installer->createOrderCollectionPointTable($setup);
        }

        if (version_compare($moduleVersion, '1.2.1', '<')) {
            $this->installer->addCollectionPointSearchPendingColumn($setup);
        }
    }
}
