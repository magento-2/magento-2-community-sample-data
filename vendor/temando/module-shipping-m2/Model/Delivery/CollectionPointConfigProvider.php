<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * Provide Collection Point Country Data.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionPointConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * @var CollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * CollectionPointConfigProvider constructor.
     * @param ModuleConfigInterface $moduleConfig
     * @param CollectionFactory $countryCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        CollectionFactory $countryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->countryCollectionFactory = $countryCollectionFactory;
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
        if (!$this->moduleConfig->isEnabled($storeId) || !$this->moduleConfig->isCollectionPointsEnabled($storeId)) {
            return ['countries' => []];
        }

        $countryCodes = $this->moduleConfig->getCollectionPointDeliveryCountries($storeId);
        $countryCollection = $this->countryCollectionFactory->create();
        $countryCollection->addFieldToFilter('country_id', ['in' => explode(',', $countryCodes)]);
        $countryCollection->loadByStore($storeId);

        return ['ts-cp-countries' => $countryCollection->toOptionArray(false)];
    }
}
