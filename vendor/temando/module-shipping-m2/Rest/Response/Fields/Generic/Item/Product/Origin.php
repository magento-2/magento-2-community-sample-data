<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Generic\Item\Product;

use Temando\Shipping\Rest\Response\Fields\Generic\Item\Product\Origin\Address;

/**
 * Temando API Item Product Origin Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Origin
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Item\Product\Origin\Address
     */
    private $address;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Item\Product\Origin\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Item\Product\Origin\Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }
}
