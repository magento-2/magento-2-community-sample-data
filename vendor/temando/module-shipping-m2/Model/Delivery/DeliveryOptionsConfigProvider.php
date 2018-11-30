<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * Provide Delivery Options Data to Checkout.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class DeliveryOptionsConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * DeliveryOptionsConfigProvider constructor.
     * @param ModuleConfigInterface $moduleConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Obtain country data for display in checkout, shipping method step.
     *
     * @return string[]
     */
    public function getConfig()
    {
        $storeId = $this->storeManager->getStore()->getId();
        if (!$this->moduleConfig->isEnabled($storeId)
            || (!$this->moduleConfig->isClickAndCollectEnabled($storeId)
                && !$this->moduleConfig->isCollectionPointsEnabled($storeId)
               )
        ) {
            return ['delivery-options' => []];
        }

        $deliveryOptions = $this->getDeliveryOptions();
        if (!$this->moduleConfig->isClickAndCollectEnabled($storeId)) {
            unset($deliveryOptions['to-pickup-store']);
        }

        if (!$this->moduleConfig->isCollectionPointsEnabled($storeId)) {
            unset($deliveryOptions['to-collection-point']);
        }

        return ['delivery-options' => array_values($deliveryOptions)];
    }

    /**
     * @return string[][]
     */
    private function getDeliveryOptions()
    {
        $deliveryOptions = [
            'to-address' => [
                'id' => 'to-address',
                'label' => 'Send To Address',
                'value' => 'toAddress'
            ],
            'to-collection-point' => [
                'id' => 'to-collection-point',
                'label' => 'Send To Collection Point',
                'value' => 'toCollectionPoint'
            ],
            'to-pickup-store' => [
                'id' => 'to-pickup-store',
                'label' => 'Pick up in Store',
                'value' => 'clickAndCollect'
            ]
        ];

        return $deliveryOptions;
    }
}
