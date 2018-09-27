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
 * Interface OrderlineInterface
 *
 * @package Klarna\Kp\Api\Data
 */
interface OrderlineInterface extends ApiObjectInterface
{
    /**
     * Orderline type
     *
     * Possible values:
     *
     * * physical (default)
     * * discount
     * * shipping_fee
     *
     * @param string $type
     */
    public function setType($type);

    /**
     * Descriptive item name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * @param string $product_url
     */
    public function setProductUrl($product_url);

    /**
     * @param string $image_url
     */
    public function setImageUrl($image_url);

    /**
     * The item quantity.
     *
     * @param int $quantity
     */
    public function setQuantity($quantity);

    /**
     * Descriptive unit, e.g. kg, pcs.
     *
     * @param string $quantity_unit
     */
    public function setQuantityUnit($quantity_unit);

    /**
     * Includes tax, excludes discount. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $unit_price
     */
    public function setUnitPrice($unit_price);

    /**
     * In percent, two implicit decimals, i.e. 2500 = 25%.
     *
     * @param int $tax_rate
     */
    public function setTaxRate($tax_rate);

    /**
     * Must be within ±1 of total_amount - total_amount 10000 / (10000 + tax_rate). Negative when type is discount.
     *
     * @param int $total_tax_amount
     */
    public function setTotalTaxAmount($total_tax_amount);

    /**
     *  Includes tax. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $total_discount_amount
     */
    public function setTotalDiscountAmount($total_discount_amount);

    /**
     * Includes tax and discount. Must match (quantity * unit_price) + total discount amount within ±quantity.
     *
     * @param int $total_amount
     */
    public function setTotalAmount($total_amount);

    /**
     * Article number, SKU or similar.
     *
     * @param string $reference
     */
    public function setReference($reference);

    /**
     * @return int
     */
    public function getTotal();
}
