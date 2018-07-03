<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Generic;

/**
 * Temando API Order Attributes Item Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Item
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Item\Product
     */
    private $product;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Item\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Item\Product $product
     * @return void
     */
    public function setProduct(\Temando\Shipping\Rest\Response\Type\Generic\Item\Product $product)
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
