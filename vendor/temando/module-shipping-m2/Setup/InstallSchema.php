<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Init module config data
 *
 * @package  Temando\Shipping\Setup
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class InstallSchema implements InstallSchemaInterface
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
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installer->createShipmentTable($setup);
        $this->installer->setShipmentOriginLocationNullable($setup);

        $this->installer->createOrderTable($setup);
        $this->installer->createAddressTable($setup);
    }
}
