<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item;

use Temando\Shipping\Rest\Response\Type\Generic\Attribute;
use Temando\Shipping\Rest\Response\Type\Generic\MonetaryValue;
use Temando\Shipping\Rest\Response\Type\Generic\Weight;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item\Product\ClassificationCodes;

/**
 * Temando API Order Attributes Item Product Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Product
{
    /**
     * @var string
     */
    private $sku;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item\Product\Manufacture
     */
    private $manufacture;

    /**
     * @var string
     */
    private $unit;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item\Product\Origin
     */
    private $origin;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Weight
     */
    private $weight;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\MonetaryValue
     */
    private $monetaryValue;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item\Product\ClassificationCodes
     */
    private $classificationCodes;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Attribute
     */
    private $customAttributes;

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return void
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return Product\Manufacture
     */
    public function getManufacture()
    {
        return $this->manufacture;
    }

    /**
     * @param Product\Manufacture $manufacture
     */
    public function setManufacture($manufacture)
    {
        $this->manufacture = $manufacture;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item\Product\Origin
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item\Product\Origin $origin
     * @return void
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Weight
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Weight $weight
     * @return void
     */
    public function setWeight(Weight $weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\MonetaryValue
     */
    public function getMonetaryValue()
    {
        return $this->monetaryValue;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\MonetaryValue $monetaryValue
     * @return void
     */
    public function setMonetaryValue(MonetaryValue $monetaryValue)
    {
        $this->monetaryValue = $monetaryValue;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item\Product\ClassificationCodes
     */
    public function getClassificationCodes()
    {
        return $this->classificationCodes;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item\Product\ClassificationCodes $classificationCodes
     * @return void
     */
    public function setClassificationCodes(ClassificationCodes $classificationCodes)
    {
        $this->classificationCodes = $classificationCodes;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Attribute
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Attribute $customAttributes
     * @return void
     */
    public function setCustomAttributes(Attribute $customAttributes)
    {
        $this->customAttributes = $customAttributes;
    }
}
