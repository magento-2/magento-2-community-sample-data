<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Api;

use Klarna\Kp\Api\Data\AddressInterface;
use Klarna\Kp\Api\Data\AttachmentInterface;
use Klarna\Kp\Api\Data\CustomerInterface;
use Klarna\Kp\Api\Data\OptionsInterface;
use Klarna\Kp\Api\Data\OrderlineInterface;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\UrlsInterface;

/**
 * Class Request
 *
 * @package Klarna\Kp\Model\Api
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Request implements RequestInterface
{
    /**
     * @var string
     */
    private $merchant_reference1;

    /**
     * @var string
     */
    private $merchant_reference2;

    /**
     * @var OptionsInterface
     */
    private $options;

    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * @var UrlsInterface
     */
    private $urls;

    /**
     * @var string
     */
    private $purchase_country;

    /**
     * @var string
     */
    private $purchase_currency;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var AddressInterface
     */
    private $billing_address;

    /**
     * @var AddressInterface
     */
    private $shipping_address;

    /**
     * @var int
     */
    private $order_tax_amount;

    /**
     * @var OrderlineInterface[]
     */
    private $order_lines;

    /**
     * @var AttachmentInterface
     */
    private $attachment;

    /**
     * @var int
     */
    private $order_amount;

    /**
     * @var string
     */
    private $design;

    /**
     * @var array
     */
    private $custom_payment_methods;

    /**
     * Request constructor.
     *
     * @param array $data
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
     * Used for storing merchant's internal order number or other reference (max 255 characters).
     *
     * @param string $reference
     */
    public function setMerchantReference1($reference)
    {
        $this->merchant_reference1 = $reference;
    }

    /**
     * Used for storing merchant's internal order number or other reference (max 255 characters).
     *
     * @param string $reference
     */
    public function setMerchantReference2($reference)
    {
        $this->merchant_reference2 = $reference;
    }

    /**
     * @param OptionsInterface $options
     */
    public function setOptions(OptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * Information about the liable customer of the order.
     *
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @param UrlsInterface $urls
     */
    public function setMerchantUrls(UrlsInterface $urls)
    {
        $this->urls = $urls;
    }

    /**
     * ISO 3166 alpha-2 => Purchase country
     *
     * @param string $purchaseCountry
     */
    public function setPurchaseCountry($purchaseCountry)
    {
        $this->purchase_country = $purchaseCountry;
    }

    /**
     * ISO 4217 => Purchase currency.
     *
     * @param string $purchaseCurrency
     */
    public function setPurchaseCurrency($purchaseCurrency)
    {
        $this->purchase_currency = $purchaseCurrency;
    }

    /**
     * RFC 1766 => Customer's locale.
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * The billing address.
     *
     * @param AddressInterface $billingAddress
     */
    public function setBillingAddress(AddressInterface $billingAddress)
    {
        $this->billing_address = $billingAddress;
    }

    /**
     * The shipping address.
     *
     * @param AddressInterface $shippingAddress
     */
    public function setShippingAddress(AddressInterface $shippingAddress)
    {
        $this->shipping_address = $shippingAddress;
    }

    /**
     * The total tax amount of the order. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $orderTaxAmount
     */
    public function setOrderTaxAmount($orderTaxAmount)
    {
        $this->order_tax_amount = $orderTaxAmount;
    }

    /**
     * The applicable order lines.
     *
     * @param array|OrderlineInterface[] $orderlines
     */
    public function setOrderLines(array $orderlines)
    {
        $this->order_lines = $orderlines;
    }

    /**
     * Add an orderline to the request.
     *
     * @param OrderlineInterface $orderline
     */
    public function addOrderLine(OrderlineInterface $orderline)
    {
        $this->order_lines[] = $orderline;
    }

    /**
     * Container for optional merchant specific data.
     *
     * @param AttachmentInterface $attachment
     */
    public function setAttachment(AttachmentInterface $attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * Total amount of the order, including tax and any discounts. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $orderAmount
     */
    public function setOrderAmount($orderAmount)
    {
        $this->order_amount = $orderAmount;
    }

    /**
     * Used to load a specific design for the credit form
     *
     * @param string $design
     */
    public function setDesign($design)
    {
        $this->design = $design;
    }

    /**
     * An optional list of merchant triggered payment methods
     *
     * @param array $paymentMethods
     */
    public function setCustomPaymentMethods(array $paymentMethods)
    {
        $this->custom_payment_methods = $paymentMethods;
    }

    /**
     * Add a merchant triggered payment method to request
     *
     * @param string $paymentMethod
     */
    public function addCustomPaymentMethods($paymentMethod)
    {
        $this->custom_payment_methods[] = $paymentMethod;
    }

    /**
     * Generate array object needed for API call
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function toArray()
    {
        $request = [
            'purchase_country'       => $this->purchase_country,
            'purchase_currency'      => $this->purchase_currency,
            'locale'                 => $this->locale,
            'order_amount'           => $this->order_amount,
            'order_tax_amount'       => $this->order_tax_amount,
            'order_lines'            => null,
            'billing_address'        => null,
            'shipping_address'       => null,
            'customer'               => null,
            'merchant_urls'          => null,
            'merchant_reference1'    => $this->merchant_reference1,
            'merchant_reference2'    => $this->merchant_reference2,
            'design'                 => $this->design,
            'options'                => null,
            'custom_payment_methods' => $this->custom_payment_methods,
            'attachment'             => null
        ];
        if (null !== $this->billing_address) {
            $request['billing_address'] = $this->billing_address->toArray();
        }
        if (null !== $this->shipping_address) {
            $request['shipping_address'] = $this->shipping_address->toArray();
        }
        if (null !== $this->customer) {
            $request['customer'] = $this->customer->toArray();
        }
        if (null !== $this->urls) {
            $request['merchant_urls'] = $this->urls->toArray();
        }
        if (null !== $this->attachment) {
            $request['attachment'] = $this->attachment->toArray();
        }
        if (null !== $this->order_lines) {
            $request['order_lines'] = [];
            foreach ($this->order_lines as $line) {
                $request['order_lines'][] = $line->toArray();
            }
        }
        if (null !== $this->options) {
            $request['options'] = $this->options->toArray();
        }
        return array_filter($request, function ($value) {
            if ($value === null) {
                return false;
            }
            if (is_array($value) && count($value) === 0) {
                return false;
            }
            return true;
        });
    }
}
