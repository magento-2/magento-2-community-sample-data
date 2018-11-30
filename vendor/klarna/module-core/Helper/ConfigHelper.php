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

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Class ConfigHelper
 *
 * @package Klarna\Core\Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigHelper extends AbstractHelper
{
    /**
     * Payment method
     *
     * @var string
     */
    private $code = '';
    /**
     * Observer event prefix
     *
     * @var string
     */
    private $eventPrefix = '';
    /**
     * @var Resolver
     */
    private $resolver;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    const KP_METHOD_CODE = 'klarna_kp';

    const KCO_METHOD_CODE = 'klarna_kco';

    /**
     * ConfigHelper constructor.
     *
     * @param Context                     $context
     * @param Resolver                    $resolver
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRepositoryInterface  $addressRepository
     * @param string                      $code
     * @param string                      $eventPrefix
     */
    public function __construct(
        Context $context,
        Resolver $resolver,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        $code = 'klarna_kp',
        $eventPrefix = 'kp'
    ) {
        parent::__construct($context);
        $this->resolver = $resolver;
        $this->code = $code;
        $this->eventPrefix = $eventPrefix;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * Get the order status that should be set on orders that have been processed by Klarna
     *
     * @param Store  $store
     * @param string $paymentMethod
     *
     * @return string
     */
    public function getProcessedOrderStatus($store = null, $paymentMethod = null)
    {
        return $this->getPaymentConfig('order_status', $store, $paymentMethod);
    }

    /**
     * Get payment config value
     *
     * @param string $config
     * @param Store  $store
     * @param string $paymentMethod
     *
     * @return mixed
     */
    public function getPaymentConfig($config, $store = null, $paymentMethod = null)
    {
        if (!$paymentMethod) {
            $paymentMethod = $this->code;
        }
        $scope = $this->getScope($store);
        return $this->scopeConfig->getValue(sprintf('payment/' . $paymentMethod . '/%s', $config), $scope, $store);
    }

    /**
     * Prepare float for API call
     *
     * @param float $float
     *
     * @return int
     * @deprecated 4.0.1
     * @see DataConverter::toApiFloat()
     */
    public function toApiFloat($float)
    {
        return round($float * 100);
    }

    /**
     * Get API config value
     *
     * @param string $config
     * @param Store  $store
     *
     * @return mixed
     */
    public function getApiConfig($config, $store = null)
    {
        $scope = $this->getScope($store);
        return $this->scopeConfig->getValue(sprintf('klarna/api/%s', $config), $scope, $store);
    }

    /**
     * Get the current locale code
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->resolver->getLocale();
    }

    /**
     * Get checkout design config value
     *
     * @param Store $store
     *
     * @return mixed
     */
    public function getCheckoutDesignConfig($store = null)
    {
        $scope = $this->getScope($store);
        $designOptions = $this->scopeConfig->getValue('checkout/' . $this->code . '_design', $scope, $store);

        return is_array($designOptions) ? $designOptions : [];
    }

    /**
     * Get base currencey for store
     *
     * @param Store $store
     *
     * @return mixed
     */
    public function getBaseCurrencyCode($store = null)
    {
        $scope = $this->getScope($store);
        return $this->scopeConfig->getValue('currency/options/base', $scope, $store);
    }

    /**
     * Get payment config value
     *
     * @param string $config
     * @param Store  $store
     * @param string $paymentMethod
     *
     * @return bool
     */
    public function isPaymentConfigFlag($config, $store = null, $paymentMethod = null)
    {
        if (null === $paymentMethod) {
            $paymentMethod = $this->code;
        }
        $scope = $this->getScope($store);
        return $this->scopeConfig->isSetFlag(sprintf('payment/' . $paymentMethod . '/%s', $config), $scope, $store);
    }

    /**
     * Get API config value
     *
     * @param string $config
     * @param Store  $store
     *
     * @return bool
     */
    public function isApiConfigFlag($config, $store = null)
    {
        $scope = $this->getScope($store);
        return $this->scopeConfig->isSetFlag(sprintf('klarna/api/%s', $config), $scope, $store);
    }

    /**
     * check if b2b mode is enabled in setting
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isB2bEnabled($store = null)
    {
        return $this->isCheckoutConfigFlag('enable_b2b', $store, $this->code);
    }

    /**
     * Get checkout config value
     *
     * @param string $config
     * @param Store  $store
     * @param string $paymentMethod
     *
     * @return bool
     */
    public function isCheckoutConfigFlag($config, $store = null, $paymentMethod = null)
    {
        if (null === $paymentMethod) {
            $paymentMethod = $this->code;
        }
        $scope = $this->getScope($store);
        return $this->scopeConfig->isSetFlag(sprintf('checkout/%s/%s', $paymentMethod, $config), $scope, $store);
    }

    /**
     * check if this customer is a business customer
     *
     * @param int   $customerId
     * @param Store $store
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isB2bCustomer($customerId, $store)
    {
        if ($customerId) {
            $businessIdValue = $this->getBusinessIdAttributeValue($customerId, $store);
            $businessNameValue = $this->getCompanyNameFromAddress($customerId);
            if (!empty($businessIdValue) || !empty($businessNameValue)) {
                return true;
            }
        }
        return false;
    }

    /**
     * get organization id value
     *
     * @param int   $customerId
     * @param store $store
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBusinessIdAttributeValue($customerId, $store)
    {
        $customerObj = $this->customerRepository->getById($customerId);
        $businessIdValue = $customerObj->getCustomAttribute($this->getBusinessIdAttribute($store));
        if ($businessIdValue) {
            return $businessIdValue->getValue();
        }
        return false;
    }

    /**
     * get the code for custom attribute for recording organization id
     *
     * @param Store $store
     * @return mixed
     */
    public function getBusinessIdAttribute($store = null)
    {
        return $this->getCheckoutConfig('business_id_attribute', $store);
    }

    /**
     * Get checkout config value
     *
     * @param string $config
     * @param Store  $store
     *
     * @return mixed
     */
    public function getCheckoutConfig($config, $store = null)
    {
        $scope = $this->getScope($store);
        return $this->scopeConfig->getValue(sprintf('checkout/%s/%s', $this->code, $config), $scope, $store);
    }

    /**
     * check if customer's default billing address contain company name
     *
     * @param int $customerId
     * @return bool|null|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCompanyNameFromAddress($customerId)
    {
        $customerObj = $this->customerRepository->getById($customerId);
        $billingAddressId = $customerObj->getDefaultBilling();
        if ($billingAddressId) {
            $defaultBillingAddress = $this->addressRepository->getById($billingAddressId);
            return $defaultBillingAddress->getCompany();
        }
        return false;
    }

    /**
     * Determine if FPT (Fixed Product Tax) is set to be included in the subtotal
     *
     * @param Store $store
     * @return int
     */
    public function getDisplayInSubtotalFpt($store = null)
    {
        $scope = $this->getScope($store);
        return $this->scopeConfig->getValue('tax/weee/include_in_subtotal', $scope, $store);
    }

    /**
     * Checking if Fixed Product Taxes are enabled
     *
     * @param Store $store
     * @return bool
     */
    public function isFptEnabled($store = null)
    {
        $scope = $this->getScope($store);
        return $this->scopeConfig->getValue('tax/weee/enable', $scope, $store);
    }

    /**
     * Get the scope value of the store
     *
     * @param Store $store
     * @return string
     */
    private function getScope($store = null)
    {
        if ($store === null) {
            return ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }
        return ScopeInterface::SCOPE_STORES;
    }
}
