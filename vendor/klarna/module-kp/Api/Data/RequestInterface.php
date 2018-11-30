<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api\Data;

/**
 * Interface RequestInterface
 *
 * @package Klarna\Kp\Api\Data
 */
interface RequestInterface extends ApiObjectInterface
{
    /**
     * Used for storing merchant's internal order number or other reference (max 255 characters).
     *
     * @param string $reference
     */
    public function setMerchantReference1($reference);

    /**
     * Used for storing merchant's internal order number or other reference (max 255 characters).
     *
     * @param string $reference
     */
    public function setMerchantReference2($reference);

    /**
     * @param OptionsInterface $options
     */
    public function setOptions(OptionsInterface $options);

    /**
     * Information about the liable customer of the order.
     *
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * @param UrlsInterface $urls
     */
    public function setMerchantUrls(UrlsInterface $urls);

    /**
     * ISO 3166 alpha-2: Purchase country
     *
     * @param string $purchaseCountry
     */
    public function setPurchaseCountry($purchaseCountry);

    /**
     * ISO 4217: Purchase currency.
     *
     * @param string $purchaseCurrency
     */
    public function setPurchaseCurrency($purchaseCurrency);

    /**
     * RFC 1766: Customer's locale.
     *
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * The billing address.
     *
     * @param AddressInterface $billingAddress
     */
    public function setBillingAddress(AddressInterface $billingAddress);

    /**
     * The shipping address.
     *
     * @param AddressInterface $shippingAddress
     */
    public function setShippingAddress(AddressInterface $shippingAddress);

    /**
     * The total tax amount of the order. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $orderTaxAmount
     */
    public function setOrderTaxAmount($orderTaxAmount);

    /**
     * The applicable order lines.
     *
     * @param array|OrderlineInterface[] $orderlines
     */
    public function setOrderLines(array $orderlines);

    /**
     * Add an orderline to the request.
     *
     * @param OrderlineInterface $orderline
     */
    public function addOrderLine(OrderlineInterface $orderline);

    /**
     * Container for optional merchant specific data.
     *
     * @param AttachmentInterface $attachment
     */
    public function setAttachment(AttachmentInterface $attachment);

    /**
     * Total amount of the order, including tax and any discounts. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $orderAmount
     */
    public function setOrderAmount($orderAmount);

    /**
     * Used to load a specific design for the credit form
     *
     * @param string $design
     */
    public function setDesign($design);

    /**
     * An optional list of merchant triggered payment methods
     *
     * @param array $paymentMethods
     */
    public function setCustomPaymentMethods(array $paymentMethods);

    /**
     * Add a merchant triggered payment method to request
     *
     * @param string $paymentMethod
     */
    public function addCustomPaymentMethods($paymentMethod);
}
