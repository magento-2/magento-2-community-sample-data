<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint;

/**
 * Temando API Collection Point Qualification Collection Point Constraints Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Constraints
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Dimensions
     */
    private $dimensions;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Value
     */
    private $weight;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Dimensions
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Dimensions $dimensions
     * @return void
     */
    public function setDimensions($dimensions)
    {
        $this->dimensions = $dimensions;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Value
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Value $weight
     * @return void
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
}
