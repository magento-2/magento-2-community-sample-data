<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Stdlib\ArrayManager;
use Temando\Shipping\Setup\SetupData;

/**
 * Product Form Dimensions Modifier
 *
 * @package  Temando\Shipping\Ui
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Dimensions extends AbstractModifier
{
    /**
     * @var LocatorInterface
     * @since 101.0.0
     */
    private $locator;

    /**
     * @var ArrayManager
     * @since 101.0.0
     */
    private $arrayManager;

    /**
     * Dimensions constructor.
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Add unit of measure to form input.
     *
     * @param mixed[] $meta
     * @return mixed[]
     */
    public function modifyMeta(array $meta)
    {
        $attributeCodes = [
            65 => SetupData::ATTRIBUTE_CODE_LENGTH,
            66 => SetupData::ATTRIBUTE_CODE_WIDTH,
            67 => SetupData::ATTRIBUTE_CODE_HEIGHT,
        ];

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->locator->getStore();
        $weightUnit = $store->getConfig(DirectoryHelper::XML_PATH_WEIGHT_UNIT);
        $attributeMeta = [
            'validation' => [
                'validate-zero-or-greater' => true
            ],
            'additionalClasses' => 'admin__field-small',
            'addafter' => ($weightUnit === 'kgs') ? 'cm' : 'in',
        ];

        foreach ($attributeCodes as $sortOrder => $attributeCode) {
            // update attribute container
            $containerName = static::CONTAINER_PREFIX . $attributeCode;
            $path = $this->arrayManager->findPath($containerName, $meta);
            $configPath = $path . static::META_CONFIG_PATH;
            $containerMeta = ['sortOrder' => $sortOrder];

            $meta = $this->arrayManager->merge($configPath, $meta, $containerMeta);

            // update attribute
            $path = $this->arrayManager->findPath($attributeCode, $meta, null, 'children');
            $configPath = $path . static::META_CONFIG_PATH;
            $attributeMeta['dataScope'] = $attributeCode;
            $attributeMeta['sortOrder'] = $sortOrder;

            $meta = $this->arrayManager->merge($configPath, $meta, $attributeMeta);
        }

        return $meta;
    }
}
