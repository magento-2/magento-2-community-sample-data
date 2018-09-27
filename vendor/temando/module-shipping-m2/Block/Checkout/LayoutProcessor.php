<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * Checkout LayoutProcessor
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * LayoutProcessor constructor.
     *
     * @param ModuleConfigInterface $moduleConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(ModuleConfigInterface $moduleConfig, StoreManagerInterface $storeManager)
    {
        $this->config = $moduleConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Process js Layout, unset delivery option for collection points based on config.
     *
     * @param mixed[] $jsLayout
     * @return mixed[]
     */
    public function process($jsLayout)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $isCheckoutEnabled = $this->config->isEnabled($storeId);
        $isCollectionPointsEnabled = $this->config->isCollectionPointsEnabled($storeId);

        if (!$isCheckoutEnabled) {
            $shippingStep = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step'];
            unset($shippingStep['children']['shippingAddress']['children']['checkoutFields']);
            // @codingStandardsIgnoreLine
            unset($shippingStep['children']['step-config']['children']['shipping-rates-validation']['children']['temando-rates-validation']);
        }

        if (!$isCheckoutEnabled || !$isCollectionPointsEnabled) {
            $shippingStep = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step'];
            unset($shippingStep['children']['shippingAddress']['children']['deliveryOptions']);
        }

        return $jsLayout;
    }
}
