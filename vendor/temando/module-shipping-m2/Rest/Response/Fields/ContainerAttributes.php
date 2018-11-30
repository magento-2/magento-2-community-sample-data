<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields;

/**
 * Temando API Container Resource Object Attributes
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class ContainerAttributes
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Dimensions
     */
    private $outerDimensions;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Dimensions
     */
    private $innerDimensions;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Value
     */
    private $maximumWeight;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Value
     */
    private $tareWeight;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Dimensions
     */
    public function getOuterDimensions()
    {
        return $this->outerDimensions;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Dimensions $outerDimensions
     * @return void
     */
    public function setOuterDimensions($outerDimensions)
    {
        $this->outerDimensions = $outerDimensions;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Dimensions
     */
    public function getInnerDimensions()
    {
        return $this->innerDimensions;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Dimensions $innerDimensions
     * @return void
     */
    public function setInnerDimensions($innerDimensions)
    {
        $this->innerDimensions = $innerDimensions;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Value
     */
    public function getMaximumWeight()
    {
        return $this->maximumWeight;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Value $maximumWeight
     * @return void
     */
    public function setMaximumWeight($maximumWeight)
    {
        $this->maximumWeight = $maximumWeight;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Value
     */
    public function getTareWeight()
    {
        return $this->tareWeight;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Value $tareWeight
     * @return void
     */
    public function setTareWeight($tareWeight)
    {
        $this->tareWeight = $tareWeight;
    }
}
