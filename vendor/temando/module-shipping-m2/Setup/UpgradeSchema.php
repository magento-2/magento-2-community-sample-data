<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Init module config data
 *
 * @package  Temando\Shipping\Setup
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
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
     * InstallSchema constructor.
     * @param SetupSchema $installer
     */
    public function __construct(SetupSchema $installer)
    {
        $this->installer = $installer;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // beware, this is the version we are upgrading from, not to!
        $moduleVersion = $context->getVersion();

        if (version_compare($moduleVersion, '0.2.6', '<')) {
            $this->installer->setShipmentOriginLocationNullable($setup);
        }

        if (version_compare($moduleVersion, '0.3.1', '<')) {
            $this->installer->createAddressTable($setup);
        }
    }
}
