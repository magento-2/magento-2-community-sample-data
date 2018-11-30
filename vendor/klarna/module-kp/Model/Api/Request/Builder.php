<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Api\Request;

use Klarna\Core\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\Data\AddressInterface;
use Klarna\Kp\Api\Data\AttachmentInterface;
use Klarna\Kp\Api\Data\CustomerInterface;
use Klarna\Kp\Api\Data\OptionsInterface;
use Klarna\Kp\Api\Data\OrderlineInterface;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\UrlsInterface;
use Klarna\Kp\Model\Api\RequestFactory;
use Magento\Framework\DataObject;

/**
 * Class Builder
 *
 * @package Klarna\Kp\Model\Api\Request
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Builder
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
     * @var int
     */
    private $order_tax_amount = 0;
    /**
     * @var int
     */
    private $order_amount = 0;
    /**
     * @var CustomerInterface
     */
    private $customer;
    /**
     * @var AttachmentInterface
     */
    private $attachment;
    /**
     * @var AddressInterface
     */
    private $billing_address;
    /**
     * @var AddressInterface
     */
    private $shipping_address;
    /**
     * @var UrlsInterface
     */
    private $merchant_urls;
    /**
     * @var OptionsInterface
     */
    private $options;
    /**
     * @var OrderlineInterface[]
     */
    private $orderlines = [];
    /**
     * @var RequestFactory
     */
    private $requestFactory;
    /**
     * @var AddressFactory
     */
    private $addressFactory;
    /**
     * @var AttachmentFactory
     */
    private $attachmentFactory;
    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var MerchantUrlsFactory
     */
    private $urlFactory;
    /**
     * @var OptionsFactory
     */
    private $optionsFactory;
    /**
     * @var OrderlineFactory
     */
    private $orderlineFactory;

    /**
     * Builder constructor.
     *
     * @param RequestFactory     $requestFactory
     * @param AddressFactory     $addressFactory
     * @param AttachmentFactory  $attachmentFactory
     * @param CustomerFactory    $customerFactory
     * @param MerchantUrlFactory $merchantUrlFactory
     * @param OptionsFactory     $optionsFactory
     * @param OrderlineFactory   $orderlineFactory
     */
    public function __construct(
        RequestFactory $requestFactory,
        AddressFactory $addressFactory,
        AttachmentFactory $attachmentFactory,
        CustomerFactory $customerFactory,
        MerchantUrlsFactory $urlFactory,
        OptionsFactory $optionsFactory,
        OrderlineFactory $orderlineFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->addressFactory = $addressFactory;
        $this->attachmentFactory = $attachmentFactory;
        $this->customerFactory = $customerFactory;
        $this->urlFactory = $urlFactory;
        $this->optionsFactory = $optionsFactory;
        $this->orderlineFactory = $orderlineFactory;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setAttachment($data)
    {
        $this->attachment = $this->attachmentFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setBillingAddress($data)
    {
        $this->billing_address = $this->addressFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setShippingAddress($data)
    {
        $this->shipping_address = $this->addressFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setMerchantUrls($data)
    {
        $this->merchant_urls = $this->urlFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setOptions($data)
    {
        $this->options = $this->optionsFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function addOrderlines($data)
    {
        foreach ($data as $key => $orderLine) {
            $this->orderlines[$key] = $this->orderlineFactory->create(['data' => $orderLine]);
        }
        return $this;
    }

    /**
     * @param DataObject $references
     * @return $this
     */
    public function setMerchantReferences($references)
    {
        $this->merchant_reference1 = $references->getData('merchant_reference_1');
        $this->merchant_reference2 = $references->getData('merchant_reference_2');
        return $this;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->requestFactory->create([
            'data' => [
                'purchase_country'    => $this->purchase_country,
                'purchase_currency'   => $this->purchase_currency,
                'locale'              => $this->locale,
                'customer'            => $this->customer,
                'options'             => $this->options,
                'order_amount'        => $this->order_amount,
                'order_tax_amount'    => $this->order_tax_amount,
                'order_lines'         => $this->orderlines,
                'urls'                => $this->merchant_urls,
                'attachment'          => $this->attachment,
                'billing_address'     => $this->billing_address,
                'shipping_address'    => $this->shipping_address,
                'merchant_reference1' => $this->merchant_reference1,
                'merchant_reference2' => $this->merchant_reference2
            ]
        ]);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setCustomer($data)
    {
        $this->customer = $this->customerFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function setOrderAmount($amount)
    {
        $this->order_amount = $amount;
        return $this;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function setOrderTaxAmount($amount)
    {
        $this->order_tax_amount = $amount;
        return $this;
    }

    /**
     * @param array  $requiredAttributes
     * @param string $type
     * @return $this
     * @throws KlarnaApiException
     */
    public function validate($requiredAttributes, $type)
    {
        $missingAttributes = [];
        foreach ($requiredAttributes as $requiredAttribute) {
            if (null === $this->$requiredAttribute) {
                $missingAttributes[] = $requiredAttribute;
            }
            if (is_array($this->$requiredAttribute) && count($this->$requiredAttribute) === 0) {
                $missingAttributes[] = $requiredAttribute;
            }
        }
        if (!empty($missingAttributes)) {
            throw new KlarnaApiException(
                __(
                    'Missing required attribute(s) on %1: "%2".',
                    $type,
                    implode(', ', $missingAttributes)
                )
            );
        }
        $total = 0;
        foreach ($this->orderlines as $orderLine) {
            $total += (int)$orderLine->getTotal();
        }

        if ($total !== $this->order_amount) {
            throw new KlarnaApiException(
                __('Order line totals do not total order_amount - %1 != %2', $total, $this->order_amount)
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPurchaseCountry()
    {
        return $this->purchase_country;
    }

    /**
     * @param string $purchase_country
     * @return Builder
     */
    public function setPurchaseCountry($purchase_country)
    {
        $this->purchase_country = $purchase_country;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return Builder
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getPurchaseCurrency()
    {
        return $this->purchase_currency;
    }

    /**
     * @param string $purchase_currency
     * @return Builder
     */
    public function setPurchaseCurrency($purchase_currency)
    {
        $this->purchase_currency = $purchase_currency;
        return $this;
    }
}
