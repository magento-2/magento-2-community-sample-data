<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Response\Fields\Fulfillment;

use Temando\Shipping\Rest\Response\Fields\Fulfillment\Item\Product;

/**
 * Temando API Fulfillment Item Field
 *
 * @package Temando\Shipping\Rest
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Item
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Fulfillment\Item\Product
     */
    private $product;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Fulfillment\Item\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Fulfillment\Item\Product $product
     * @return void
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    /**
     * @param string $quantity
     * @return void
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
}
