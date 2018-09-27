<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

/**
 * Configuration paths storage
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Klarna\Core\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Config
{
    const CONFIG_XML_PATH_KLARNA_DEBUG                      = 'klarna/api/debug';
    const CONFIG_XML_PATH_KLARNA_TEST_MODE                  = 'klarna/api/test_mode';
    const CONFIG_XML_PATH_GENERAL_STORE_INFORMATION_COUNTRY = 'general/store_information/country_id';
    const CONFIG_XML_PATH_GENERAL_STATE_OPTIONS             = 'general/region/state_required';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check what taxes should be applied after discount
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function storeAddressSet($store = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_GENERAL_STORE_INFORMATION_COUNTRY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get configuration setting "Apply Discount On Prices Including Tax" value
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function debugModeWhileLive($store = null)
    {
        if ($this->testMode($store)) {
            return false;
        }
        return $this->debugMode($store);
    }

    /**
     * Get defined tax calculation algorithm
     *
     * @param   null|string|bool|int|Store $store
     * @return  string
     */
    public function testMode($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_XML_PATH_KLARNA_TEST_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get tax class id specified for shipping tax estimation
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function debugMode($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_XML_PATH_KLARNA_DEBUG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return a list of countries that incorrectly have the state/region marked as required
     *
     * @return array
     */
    public function requiredRegions()
    {
        $failed = [];
        $knownCountriesWithOptionalRegion = [
            'at',
            'de',
            'fi',
        ];
        $countries = $this->scopeConfig->getValue(self::CONFIG_XML_PATH_GENERAL_STATE_OPTIONS);
        $countries = explode(',', $countries);
        foreach ($knownCountriesWithOptionalRegion as $country) {
            if (in_array($country, $countries)) {
                $failed[] = $country;
            }
        }
        return $failed;
    }

    /**
     * Determine if a Klarna Payment method is enabled
     *
     * @param StoreInterface $store
     * @return bool
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function klarnaEnabled($store = null)
    {
        // It is expected that this method will have plugins added by other modules. $store is required in those cases.
        return false;
    }
}
