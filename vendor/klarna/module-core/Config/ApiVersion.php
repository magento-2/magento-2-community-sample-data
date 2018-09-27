<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Config;

use Klarna\Core\Api\VersionInterface;

/**
 * Class ApiVersion
 *
 * @package Klarna\Core\Config
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ApiVersion implements VersionInterface
{
    /**
     * @var string
     */
    private $type = 'payments';
    /**
     * @var string
     */
    private $code = 'kp_na';
    /**
     * @var bool
     */
    private $shipping_callback_support = false;
    /**
     * @var bool
     */
    private $merchant_checkbox_support = false;
    /**
     * @var bool
     */
    private $date_of_birth_mandatory_support = false;
    /**
     * @var bool
     */
    private $phone_mandatory_support = false;
    /**
     * @var string
     */
    private $ordermanagement;
    /**
     * @var bool
     */
    private $title_mandatory_support = false;
    /**
     * @var bool
     */
    private $delayed_push_notification = false;
    /**
     * @var bool
     */
    private $partial_payment_disabled = false;
    /**
     * @var bool
     */
    private $separate_tax_line = false;
    /**
     * @var bool
     */
    private $shipping_in_iframe = false;
    /**
     * @var bool
     */
    private $cart_totals_in_iframe = false;
    /**
     * @var bool
     */
    private $packstation_support = false;
    /**
     * @var string
     */
    private $production_url = 'https://api.klarna.com';
    /**
     * @var string
     */
    private $testdrive_url = 'https://api.playground.klarna.com';
    /**
     * @var bool
     */
    private $payment_review = false;
    /**
     * @var string
     */
    private $label = '';

    /**
     * ApiVersion constructor.
     *
     * @param string $type
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return bool
     */
    public function isCartTotalsInIframe()
    {
        return $this->cart_totals_in_iframe;
    }

    /**
     * @param bool $cart_totals_in_iframe
     * @return ApiVersion
     */
    public function setCartTotalsInIframe($cart_totals_in_iframe)
    {
        $this->cart_totals_in_iframe = $cart_totals_in_iframe;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPackstationSupport()
    {
        return $this->packstation_support;
    }

    /**
     * @param bool $packstation_support
     * @return ApiVersion
     */
    public function setPackstationSupport($packstation_support)
    {
        $this->packstation_support = $packstation_support;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return ApiVersion
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShippingCallbackSupport()
    {
        return $this->shipping_callback_support;
    }

    /**
     * @param mixed $shipping_callback_support
     * @return ApiVersion
     */
    public function setShippingCallbackSupport($shipping_callback_support)
    {
        $this->shipping_callback_support = $shipping_callback_support;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMerchantCheckboxSupport()
    {
        return $this->merchant_checkbox_support;
    }

    /**
     * @param mixed $merchant_checkbox_support
     * @return ApiVersion
     */
    public function setMerchantCheckboxSupport($merchant_checkbox_support)
    {
        $this->merchant_checkbox_support = $merchant_checkbox_support;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDateOfBirthMandatorySupport()
    {
        return $this->date_of_birth_mandatory_support;
    }

    /**
     * @param mixed $date_of_birth_mandatory_support
     * @return ApiVersion
     */
    public function setDateOfBirthMandatorySupport($date_of_birth_mandatory_support)
    {
        $this->date_of_birth_mandatory_support = $date_of_birth_mandatory_support;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPhoneMandatorySupport()
    {
        return $this->phone_mandatory_support;
    }

    /**
     * @param mixed $phone_mandatory_support
     * @return ApiVersion
     */
    public function setPhoneMandatorySupport($phone_mandatory_support)
    {
        $this->phone_mandatory_support = $phone_mandatory_support;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrdermanagement()
    {
        return $this->ordermanagement;
    }

    /**
     * @param mixed $ordermanagement
     * @return ApiVersion
     */
    public function setOrdermanagement($ordermanagement)
    {
        $this->ordermanagement = $ordermanagement;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTitleMandatorySupport()
    {
        return $this->title_mandatory_support;
    }

    /**
     * @param mixed $title_mandatory_support
     * @return ApiVersion
     */
    public function setTitleMandatorySupport($title_mandatory_support)
    {
        $this->title_mandatory_support = $title_mandatory_support;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDelayedPushNotification()
    {
        return $this->delayed_push_notification;
    }

    /**
     * @param mixed $delayed_push_notification
     * @return ApiVersion
     */
    public function setDelayedPushNotification($delayed_push_notification)
    {
        $this->delayed_push_notification = $delayed_push_notification;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPartialPaymentDisabled()
    {
        return $this->partial_payment_disabled;
    }

    /**
     * @param mixed $partial_payment_disabled
     * @return ApiVersion
     */
    public function setPartialPaymentDisabled($partial_payment_disabled)
    {
        $this->partial_payment_disabled = $partial_payment_disabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSeparateTaxLine()
    {
        return $this->separate_tax_line;
    }

    /**
     * @param mixed $separate_tax_line
     * @return ApiVersion
     */
    public function setSeparateTaxLine($separate_tax_line)
    {
        $this->separate_tax_line = $separate_tax_line;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShippingInIframe()
    {
        return $this->shipping_in_iframe;
    }

    /**
     * @param mixed $shipping_in_iframe
     * @return ApiVersion
     */
    public function setShippingInIframe($shipping_in_iframe)
    {
        $this->shipping_in_iframe = $shipping_in_iframe;
        return $this;
    }

    /**
     * @param bool $testmode
     * @return string
     */
    public function getUrl($testmode = true)
    {
        if ($testmode) {
            return $this->getTestdriveUrl();
        }
        return $this->getProductionUrl();
    }

    /**
     * @return string
     */
    public function getTestdriveUrl()
    {
        return $this->testdrive_url;
    }

    /**
     * @param mixed $testdrive_url
     * @return ApiVersion
     */
    public function setTestdriveUrl($testdrive_url)
    {
        $this->testdrive_url = $testdrive_url;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductionUrl()
    {
        return $this->production_url;
    }

    /**
     * @param mixed $production_url
     * @return ApiVersion
     */
    public function setProductionUrl($production_url)
    {
        $this->production_url = $production_url;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPaymentReview()
    {
        return $this->payment_review;
    }

    /**
     * @param bool $payment_review
     * @return ApiVersion
     */
    public function setPaymentReview($payment_review)
    {
        $this->payment_review = $payment_review;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return ApiVersion
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return ApiVersion
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
}
