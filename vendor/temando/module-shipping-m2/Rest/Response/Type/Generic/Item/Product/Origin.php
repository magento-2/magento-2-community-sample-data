<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Generic\Item\Product;

use Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Origin\Address;

/**
 * Temando API Item Product Origin Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Origin
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Origin\Address
     */
    private $address;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Origin\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Origin\Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }
}
