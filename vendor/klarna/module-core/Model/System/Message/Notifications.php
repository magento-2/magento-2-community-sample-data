<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\System\Message;

use Ramsey\Uuid\Uuid;

/**
 * Notifications class
 */
class Notifications implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * Store manager object
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * Klarna configuration object
     *
     * @var \Klarna\Core\Model\Config
     */
    private $klarnaConfig;

    /**
     * List of countries where region is marked as required and should not be
     *
     * @var array
     */
    private $regionRequired;

    /**
     * @var \Ramsey\Uuid\UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface            $urlBuilder
     * @param \Klarna\Core\Model\Config                  $klarnaConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Klarna\Core\Model\Config $klarnaConfig
    ) {
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->klarnaConfig = $klarnaConfig;
        /**
         * Temp solution because Magento 2.1 DI breaks on classes with type-hints
         * @codingStandardsIgnoreLine
         */
        $this->uuidFactory = new \Ramsey\Uuid\UuidFactory();
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->generateIdForData('KLARNA_CONFIG_NOTIFICATION');
    }

    /**
     * Borrowed function from Magento 2.2. See \Magento\Framework\DataObject\IdentityService
     *
     * @param $data
     * @return string
     */
    private function generateIdForData($data)
    {
        // Cannot use \Magento\Framework\DataObject\IdentityGeneratorInterface because
        // it doesn't exist in Magento 2.1
        $uuid = $this->uuidFactory->uuid3(Uuid::NAMESPACE_DNS, $data);
        return $uuid->toString();
    }

    /**
     * Check whether notification is displayed
     * Checks if any of these settings are being ignored or valid:
     *      1. Wrong discount settings
     *      2. Wrong display settings
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if (!$this->isKlarnaEnabled()) {
            return false;
        }

        $this->storesWithMissingAddressSettings = $this->getStoresWithMissingAddressSettings();
        $this->storesWithDebugWhileLiveSettings = $this->getStoresWithDebugWhileLiveSettings();
        $this->regionRequired = $this->getRegionRequired();

        // Check if we have valid Klarna notifications
        return (!empty($this->storesWithMissingAddressSettings))
            || (!empty($this->storesWithDebugWhileLiveSettings))
            || (!empty($this->regionRequired));
    }

    /**
     * Check to see if any store or default has a Klarna payment method enabled
     *
     * @return bool
     */
    private function isKlarnaEnabled()
    {
        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if ($this->klarnaConfig->klarnaEnabled($store)) {
                return true;
            }
        }
        return $this->klarnaConfig->klarnaEnabled();
    }

    /**
     * Return list of store names which have not compatible tax calculation type and price display settings.
     * Return true if settings are wrong for default store.
     *
     * @return array
     */
    public function getStoresWithMissingAddressSettings()
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if (!$this->checkAddressSettings($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }
        return $storeNames;
    }

    /**
     * Check if tax calculation type and price display settings are compatible
     *
     * Invalid settings if
     *      Tax Calculation Method Based On 'Total' or 'Row'
     *      and at least one Price Display Settings has 'Including and Excluding Tax' value
     *
     * @param null|int|bool|string|\Magento\Store\Model\Store $store $store
     * @return bool
     */
    public function checkAddressSettings($store = null)
    {
        return $this->klarnaConfig->storeAddressSet($store);
    }

    /**
     * Return list of store names where tax discount settings are compatible.
     * Return true if settings are wrong for default store.
     *
     * @return array
     */
    public function getStoresWithDebugWhileLiveSettings()
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if ($this->checkDebugSettings($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }
        return $storeNames;
    }

    /**
     * Check if tax discount settings are compatible
     *
     * Matrix for invalid discount settings is as follows:
     *      Before Discount / Excluding Tax
     *      Before Discount / Including Tax
     *
     * @param null|int|bool|string|\Magento\Store\Model\Store $store $store
     * @return bool
     */
    public function checkDebugSettings($store = null)
    {
        return $this->klarnaConfig->debugModeWhileLive($store);
    }

    public function getRegionRequired()
    {
        return $this->klarnaConfig->requiredRegions();
    }

    /**
     * Build message text
     * Determine which notification and data to display
     *
     * @return string
     */
    public function getText()
    {
        $messageDetails = '';

        if (!empty($this->storesWithMissingAddressSettings)) {
            $messageDetails .= '<strong>';
            $messageDetails .= __('Warning store address has not been set under Store Information.');
            $messageDetails .= '</strong><p>';
            $messageDetails .= __('Store(s) affected: ');
            $messageDetails .= implode(', ', $this->storesWithMissingAddressSettings);
            $messageDetails .= '</p><p>';
            $messageDetails .= __(
                'Click here to go to <a href="%1">General Configuration</a> and change your settings.',
                $this->getManageGeneralUrl()
            );
            $messageDetails .= "</p>";
        }

        if (!empty($this->storesWithDebugWhileLiveSettings)) {
            $messageDetails .= '<strong>';
            $messageDetails .= __(
                'Warning debug mode should only be enabled when test mode is active'
            );
            $messageDetails .= '</strong><p>';
            $messageDetails .= __('Store(s) affected: ');
            $messageDetails .= implode(', ', $this->storesWithDebugWhileLiveSettings);
            $messageDetails .= '</p><p>';
            $messageDetails .= __(
                'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
                $this->getManageKlarnaUrl()
            );
            $messageDetails .= "</p>";
        }

        if (!empty($this->regionRequired)) {
            $messageDetails .= '<strong>';
            $messageDetails .= __(
                'Warning the following countries are configured to require a region'
            );
            $messageDetails .= '</strong><p>';
            $messageDetails .= implode(', ', $this->regionRequired);
            $messageDetails .= '</p><p>';
            $messageDetails .= __(
                'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
                $this->getManageRegionsUrl()
            );
            $messageDetails .= "</p>";
        }

        return $messageDetails;
    }

    /**
     * Get URL to the admin General configuration page
     *
     * @return string
     */
    public function getManageGeneralUrl()
    {
        return $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/store_information');
    }

    /**
     * Get URL to the admin Klarna configuration page
     *
     * @return string
     */
    public function getManageKlarnaUrl()
    {
        return $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/klarna');
    }

    public function getManageRegionsUrl()
    {
        return $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/general');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
