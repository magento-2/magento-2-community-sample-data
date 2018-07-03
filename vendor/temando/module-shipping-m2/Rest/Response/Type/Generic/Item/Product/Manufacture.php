<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Generic\Item\Product;

/**
 * Temando API Item Product Manufacture Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Manufacture
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Manufacture\Address
     */
    private $address;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Manufacture\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Manufacture\Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }
}
