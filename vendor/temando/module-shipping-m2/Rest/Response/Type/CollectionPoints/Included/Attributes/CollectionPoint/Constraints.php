<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint;

/**
 * Temando API Order Included Collection Point Constraints Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Constraints
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Dimensions
     */
    private $dimensions;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Value
     */
    private $weight;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Dimensions
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Dimensions $dimensions
     */
    public function setDimensions($dimensions)
    {
        $this->dimensions = $dimensions;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Value
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Value $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
}
