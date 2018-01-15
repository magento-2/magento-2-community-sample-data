<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Shipment\Attributes;

/**
 * Temando API Shipment Package Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Package
{
    /**
     * @var string
     */
    private $trackingReference;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Weight
     */
    private $grossWeight;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Documentation[]
     */
    private $documentation = [];

    /**
     * @var string
     */
    private $id;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Dimensions
     */
    private $dimensions;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item[]
     */
    private $items = [];

    /**
     * @return string
     */
    public function getTrackingReference()
    {
        return $this->trackingReference;
    }

    /**
     * @param string $trackingReference
     * @return void
     */
    public function setTrackingReference($trackingReference)
    {
        $this->trackingReference = $trackingReference;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Weight
     */
    public function getGrossWeight()
    {
        return $this->grossWeight;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Weight $grossWeight
     * @return void
     */
    public function setGrossWeight(\Temando\Shipping\Rest\Response\Type\Generic\Weight $grossWeight)
    {
        $this->grossWeight = $grossWeight;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Documentation[]
     */
    public function getDocumentation()
    {
        return $this->documentation;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Documentation[] $documentation
     * @return void
     */
    public function setDocumentation(array $documentation)
    {
        $this->documentation = $documentation;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Dimensions
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Dimensions $dimensions
     * @return void
     */
    public function setDimensions(\Temando\Shipping\Rest\Response\Type\Generic\Dimensions $dimensions)
    {
        $this->dimensions = $dimensions;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item[] $items
     * @return void
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }
}
