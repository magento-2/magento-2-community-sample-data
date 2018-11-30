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

use Klarna\Kp\Api\Data\OrderlineInterface;

/**
 * Class Orderline
 *
 * @package Klarna\Kp\Model\Api\Request
 */
class Orderline implements OrderlineInterface
{
    use \Klarna\Kp\Model\Api\Export;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $quantity_unit;

    /**
     * @var int
     */
    private $unit_price;

    /**
     * @var int
     */
    private $tax_rate;

    /**
     * @var int
     */
    private $total_amount;

    /**
     * @var int
     */
    private $total_discount_amount;

    /**
     * @var int
     */
    private $total_tax_amount;

    /**
     * @var string
     */
    private $product_url;

    /**
     * @var string
     */
    private $image_url;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->exports[] = $key;
            }
        }
    }

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
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Descriptive item name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $product_url
     */
    public function setProductUrl($product_url)
    {
        $this->product_url = $product_url;
    }

    /**
     * @param string $image_url
     */
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
    }

    /**
     * The item quantity.
     *
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Descriptive unit, e.g. kg, pcs.
     *
     * @param string $quantity_unit
     */
    public function setQuantityUnit($quantity_unit)
    {
        $this->quantity_unit = $quantity_unit;
    }

    /**
     * Includes tax, excludes discount. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $unit_price
     */
    public function setUnitPrice($unit_price)
    {
        $this->unit_price = $unit_price;
    }

    /**
     * In percent, two implicit decimals, i.e. 2500 = 25%.
     *
     * @param int $tax_rate
     */
    public function setTaxRate($tax_rate)
    {
        $this->tax_rate = $tax_rate;
    }

    /**
     * Must be within ±1 of total_amount - total_amount 10000 / (10000 + tax_rate). Negative when type is discount.
     *
     * @param int $total_tax_amount
     */
    public function setTotalTaxAmount($total_tax_amount)
    {
        $this->total_tax_amount = $total_tax_amount;
    }

    /**
     *  Includes tax. Implicit decimal (eg 1000 instead of 10.00)
     *
     * @param int $total_discount_amount
     */
    public function setTotalDiscountAmount($total_discount_amount)
    {
        $this->total_discount_amount = $total_discount_amount;
    }

    /**
     * Includes tax and discount. Must match (quantity * unit_price) + total discount amount within ±quantity.
     *
     * @param int $total_amount
     */
    public function setTotalAmount($total_amount)
    {
        $this->total_amount = $total_amount;
    }

    /**
     * Article number, SKU or similar.
     *
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total_amount;
    }
}
