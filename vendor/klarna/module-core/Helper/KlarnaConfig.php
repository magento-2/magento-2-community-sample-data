<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Helper;

use Klarna\Core\Api\VersionInterface;
use Klarna\Core\Api\VersionInterfaceFactory;
use Klarna\Core\Exception as KlarnaException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Config\DataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Class KlarnaConfig
 *
 * @package Klarna\Core\Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class KlarnaConfig extends AbstractHelper
{
    /**
     * Observer event prefix
     *
     * @var string
     */
    private $eventPrefix = '';
    /**
     * Configuration cache for api versions
     *
     * @var array
     */
    private $versionConfigCache = [];
    /**
     * @var DataInterface
     */
    private $config;
    /**
     * @var VersionInterfaceFactory
     */
    private $versionFactory;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * ConfigHelper constructor.
     *
     * @param Context                 $context
     * @param DataInterface           $config
     * @param VersionInterfaceFactory $versionFactory
     * @param DataObjectFactory       $dataObjectFactory
     * @param string                  $eventPrefix
     */
    public function __construct(
        Context $context,
        DataInterface $config,
        VersionInterfaceFactory $versionFactory,
        DataObjectFactory $dataObjectFactory,
        $eventPrefix = 'kp'
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->eventPrefix = $eventPrefix;
        $this->versionFactory = $versionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Get the current checkout api type code
     *
     * @param Store $store
     *
     * @return string
     * @throws KlarnaException
     */
    public function getCheckoutType($store = null)
    {
        return $this->getVersionConfig($store)->getType();
    }

    /**
     * Get configuration parameters for API version
     *
     * @param string $version
     * @return VersionInterface
     * @throws KlarnaException
     */
    public function getVersionConfig($store)
    {
        $scope = ($store === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        $version = $this->scopeConfig->getValue('klarna/api/api_version', $scope, $store);
        if ($version === null) {
            throw new KlarnaException(__('Invalid Api Version: ' . $version));
        }
        if (!array_key_exists($version, $this->versionConfigCache)) {
            $this->versionConfigCache[$version] = $this->getCheckoutVersionDetails($version);
        }

        return $this->versionConfigCache[$version];
    }

    /**
     * Get api version details
     *
     * @param string $code
     *
     * @return VersionInterface
     * @throws KlarnaException
     */
    public function getCheckoutVersionDetails($code)
    {
        $options = $this->getConfig(sprintf('api_versions/%s', $code));
        if ($options === null) {
            $options = [];
        }
        if (!is_array($options)) {
            $options = [$options];
        }
        if (isset($options['options'])) {
            $options = array_merge($options, $options['options']);
            unset($options['options']);
        }
        $options['code'] = $code;

        $apiTypeConfig = $this->getApiTypeConfig($options['type']);
        $apiTypeOptions = $apiTypeConfig->getOptions();
        $apiTypeOptions['ordermanagement'] = $apiTypeConfig->getOrdermanagement();
        $options = array_merge($apiTypeOptions, $options);
        /** @var VersionInterface $optionsObject */
        $optionsObject = $this->versionFactory->create(['data' => $options]);

        return $this->fireEvent($this->eventPrefix . '_load_version_details', 'options', $optionsObject);
    }

    /**
     * Get Klarna config value for $key
     *
     * @param $key
     * @return mixed
     * @throws \RuntimeException
     */
    private function getConfig($key)
    {
        return $this->config->get($key);
    }

    /**
     * Get api type configuration
     *
     * @param string $code
     *
     * @return DataObject
     * @throws KlarnaException
     */
    public function getApiTypeConfig($code)
    {
        $typeConfig = $this->getConfig(sprintf('api_types/%s', $code));
        if (!$typeConfig) {
            throw new KlarnaException(__('Invalid API version selected!'));
        }

        return $this->fireEvent($this->eventPrefix . '_load_api_config', 'options', $typeConfig);
    }

    /**
     * @param string          $eventName
     * @param string          $objectName
     * @param array|\stdClass $objectData
     * @return mixed
     */
    private function fireEvent($eventName, $objectName, $dataObject)
    {
        if (is_array($dataObject)) {
            $dataObject = $this->dataObjectFactory->create(['data' => $dataObject]);
        }
        $eventData = [
            $objectName => $dataObject
        ];

        $this->_eventManager->dispatch($eventName, $eventData);
        return $dataObject;
    }

    /**
     * Get order line times from klarna.xml file
     *
     * @param string $checkoutType
     * @return string[][]
     */
    public function getOrderlines($checkoutType)
    {
        return $this->getConfig(sprintf('order_lines/%s', $checkoutType));
    }

    /**
     * Get merchant checkbox method configuration details
     *
     * @param string $code
     *
     * @return DataObject
     */
    public function getMerchantCheckboxMethodConfig($code)
    {
        $options = $this->getConfig(sprintf('merchant_checkbox/%s', $code));
        if ($options === null) {
            $options = [];
        }
        if (!is_array($options)) {
            $options = [$options];
        }
        $options['code'] = $code;

        return $this->dataObjectFactory->create(['data' => $options]);
    }

    /**
     * Determine if current store allows shipping callbacks
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isShippingCallbackSupport($store = null)
    {
        return $this->getVersionConfig($store)->isShippingCallbackSupport();
    }

    /**
     * Determine if current store supports the use of the merchant checkbox feature
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isMerchantCheckboxSupport($store = null)
    {
        return $this->getVersionConfig($store)->isMerchantCheckboxSupport();
    }

    /**
     * Determine if current store supports the use of date of birth mandatory
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isDateOfBirthMandatorySupport($store = null)
    {
        return $this->getVersionConfig($store)->isDateOfBirthMandatorySupport();
    }

    /**
     * Determine if current store supports the use of phone mandatory
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isPhoneMandatorySupport($store = null)
    {
        return $this->getVersionConfig($store)->isPhoneMandatorySupport();
    }

    /**
     * Determine if current store supports the use of phone mandatory
     *
     * @param Store $store
     *
     * @return string
     * @throws KlarnaException
     */
    public function getOrderMangagementClass($store = null)
    {
        return $this->getVersionConfig($store)->getOrdermanagement();
    }

    /**
     * Determine if current store supports the use of title mandatory
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isTitleMandatorySupport($store = null)
    {
        return $this->getVersionConfig($store)->isTitleMandatorySupport();
    }

    /**
     * Determine if current store has a delayed push notification from Klarna
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isDelayedPushNotification($store = null)
    {
        return $this->getVersionConfig($store)->isDelayedPushNotification();
    }

    /**
     * Determine if current store supports the use of partial captures and refunds
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isPartialPaymentSupport($store = null)
    {
        return !$this->getVersionConfig($store)->isPartialPaymentDisabled();
    }

    /**
     * Return Builder Type to use in OM requests
     *
     * @param VersionInterface $versionConfig
     * @param string           $methodCode
     * @return null|string
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function getOmBuilderType(VersionInterface $versionConfig, $methodCode = 'klarna_kp')
    {
        // It is expected that this method will have plugins added by other modules.
        // $versionConfig and $methodCode are required in those cases.
        return null;
    }

    /**
     * @param $store
     * @return bool
     * @throws KlarnaException
     */
    public function isSeparateTaxLine($store)
    {
        return $this->getVersionConfig($store)->isSeparateTaxLine();
    }

    /**
     * @param $store
     * @return bool
     * @throws KlarnaException
     * @deprecated 4.4.0
     * @see \Klarna\Kco\Helper\Checkout::isShippingInIframe
     */
    public function isShippingInIframe($store)
    {
        return $this->getVersionConfig($store)->isShippingInIframe();
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getExternalPaymentOptions($code)
    {
        return $this->getConfig(sprintf('external_payment_methods/%s', $code));
    }
}
