<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Generic\Item\Product;

/**
 * Temando API Item Product Manufacture Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Manufacture
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Item\Product\Manufacture\Address
     */
    private $address;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Item\Product\Manufacture\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Item\Product\Manufacture\Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }
}
