<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * Provide Collection Point Country Data.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
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
     * CollectionPointConfigProvider constructor.
     * @param ModuleConfigInterface $moduleConfig
     * @param CollectionFactory $countryCollectionFactory
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        CollectionFactory $countryCollectionFactory
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * Obtain country data for display in checkout, shipping method step.
     *
     * @return string[]
     */
    public function getConfig()
    {
        if (!$this->moduleConfig->isEnabled() || !$this->moduleConfig->isCollectionPointsEnabled()) {
            return ['countries' => []];
        }

        $countryCodes = $this->moduleConfig->getCollectionPointDeliveryCountries();
        $countryCollection = $this->countryCollectionFactory->create();
        $countryCollection->addFieldToFilter('country_id', ['in' => $countryCodes]);
        $countryCollection->loadByStore();

        return ['ts-cp-countries' => $countryCollection->toOptionArray(false)];
    }
}
