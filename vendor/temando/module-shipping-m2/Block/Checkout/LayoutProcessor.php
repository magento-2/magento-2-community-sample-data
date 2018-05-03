<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Model\Config\ModuleConfig;

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
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * LayoutProcessor constructor.
     *
     * @param ModuleConfig          $moduleConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(ModuleConfig $moduleConfig, StoreManagerInterface $storeManager)
    {
        $this->moduleConfig = $moduleConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Process js Layout, unset delivery option for collection points based on config.
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if (!$this->moduleConfig->isEnabled($this->storeManager->getStore()->getId())) {
            $shippingStep = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step'];
            unset($shippingStep['children']['shippingAddress']['children']['checkoutFields']);
            unset($shippingStep['children']['step-config']['children']['shipping-rates-validation']['children']['temando-rates-validation']);
        }

        return $jsLayout;
    }
}
