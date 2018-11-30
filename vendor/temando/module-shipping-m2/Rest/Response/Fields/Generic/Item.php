<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Generic;

/**
 * Temando API Item Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Item
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Item\Product
     */
    private $product;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Item\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Item\Product $product
     * @return void
     */
    public function setProduct(\Temando\Shipping\Rest\Response\Fields\Generic\Item\Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return void
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
}
