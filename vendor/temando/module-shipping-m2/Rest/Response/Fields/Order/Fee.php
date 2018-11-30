<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Order;

/**
 * Temando API Order Fee Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Fee
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    private $cost;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }
}
