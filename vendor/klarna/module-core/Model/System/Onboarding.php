<?php

/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\System;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Onboarding
 *
 * @package Klarna\Core\Model\System
 */
class Onboarding
{

    /** @var ProductMetadata $productMetaData */
    private $productMetaData;

    /** @var ScopeConfigInterface $scopeConfig */
    private $scopeConfig;

    /** @var Http $http */
    private $http;

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /**
     * Onboarding Constructor
     *
     * @param ProductMetadata       $productMetadata
     * @param ScopeConfigInterface  $scopeConfig
     * @param Http                  $http
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductMetadata $productMetadata,
        ScopeConfigInterface $scopeConfig,
        Http $http,
        StoreManagerInterface $storeManager
    ) {
        $this->productMetaData = $productMetadata;
        $this->scopeConfig = $scopeConfig;
        $this->http = $http;
        $this->storeManager = $storeManager;
    }

    /**
     * Get Onboarding URL
     *
     * @param string $moduleKey
     * @param string $moduleVersion
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUrl($moduleKey, $moduleVersion)
    {
        $platform = 'magento';
        $platformVersion = $this->productMetaData->getVersion();

        $queryParameter = sprintf(
            '?plugin=%s&pluginVersion=%s&platform=%s&platformVersion=%sproducts=%s',
            $moduleKey,
            $moduleVersion,
            $platform,
            $platformVersion,
            $moduleKey
        );

        $websiteId = $this->http->getParam('website', 0);
        $website = $this->storeManager->getWebsite($websiteId);

        $scope = $this->getScope($website);
        $country = $this->scopeConfig->getValue('general/store_information/country_id', $scope, $website);

        if (!empty($country)) {
            $queryParameter .= '&country=' . $country;
        }

        $url = 'https://eu.portal.klarna.com/signup' . $queryParameter;
        if ($country === 'US') {
            $url = 'https://us.portal.klarna.com/signup' . $queryParameter;
        }

        return $url;
    }

    /**
     * Return either website scope or default scope depending on value of $website
     *
     * @param \Magento\Store\Api\Data\WebsiteInterface $website
     * @return string
     */
    private function getScope($website = null)
    {
        if ($website === null) {
            return ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return ScopeInterface::SCOPE_WEBSITES;
    }
}
