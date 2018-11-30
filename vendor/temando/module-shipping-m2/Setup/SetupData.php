<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Data setup for use during installation / upgrade
 *
 * @package  Temando\Shipping\Setup
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class SetupData
{
    const ATTRIBUTE_CODE_LENGTH = 'ts_dimensions_length';
    const ATTRIBUTE_CODE_WIDTH = 'ts_dimensions_width';
    const ATTRIBUTE_CODE_HEIGHT = 'ts_dimensions_height';

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * SetupData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Add dimension attributes. Need to be editable on store level due to the
     * weight unit (that dimensions unit is derived from) is configurable on
     * store level.
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function addDimensionAttributes(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(Product::ENTITY, self::ATTRIBUTE_CODE_LENGTH, [
            'type' => 'decimal',
            'label' => 'Length',
            'input' => 'text',
            'required' => false,
            'class' => 'not-negative-amount',
            'sort_order' => 65,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'user_defined' => false,
            'apply_to' => Type::TYPE_SIMPLE
        ]);

        $eavSetup->addAttribute(Product::ENTITY, self::ATTRIBUTE_CODE_WIDTH, [
            'type' => 'decimal',
            'label' => 'Width',
            'input' => 'text',
            'required' => false,
            'class' => 'not-negative-amount',
            'sort_order' => 66,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'user_defined' => false,
            'apply_to' => Type::TYPE_SIMPLE
        ]);

        $eavSetup->addAttribute(Product::ENTITY, self::ATTRIBUTE_CODE_HEIGHT, [
            'type' => 'decimal',
            'label' => 'Height',
            'input' => 'text',
            'required' => false,
            'class' => 'not-negative-amount',
            'sort_order' => 67,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'user_defined' => false,
            'apply_to' => Type::TYPE_SIMPLE
        ]);
    }
}
