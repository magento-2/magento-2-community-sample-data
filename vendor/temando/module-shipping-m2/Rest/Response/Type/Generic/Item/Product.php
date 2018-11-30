<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Generic\Item;

use Temando\Shipping\Rest\Response\Type\Generic\Attribute;
use Temando\Shipping\Rest\Response\Type\Generic\Dimensions;
use Temando\Shipping\Rest\Response\Type\Generic\MonetaryValue;
use Temando\Shipping\Rest\Response\Type\Generic\Value;
use Temando\Shipping\Rest\Response\Type\Generic\Item\Product\ClassificationCodes;

/**
 * Temando API Item Product Response Type
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
     * @var string[]
     */
    private $categories = [];

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $unit;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Dimensions
     */
    private $dimensions;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Value
     */
    private $weight;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\MonetaryValue
     */
    private $monetaryValue;

    /**
     * @var bool
     */
    private $isFragile;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Origin
     */
    private $origin;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Manufacture
     */
    private $manufacture;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\ClassificationCodes
     */
    private $classificationCodes;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $modifiedAt;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Attribute
     */
    private $customAttributes;

    /**
     * @var bool
     */
    private $isVirtual;

    /**
     * @var bool
     */
    private $isPrePackaged;

    /**
     * @var bool
     */
    private $canRotateVertical;

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
     * @return string[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param string[] $categories
     * @return void
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
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
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return void
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
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
    public function setDimensions(Dimensions $dimensions)
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
     * @return void
     */
    public function setWeight(Value $weight)
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
     * @return bool
     */
    public function getIsFragile()
    {
        return $this->isFragile;
    }

    /**
     * @param bool $isFragile
     * @return void
     */
    public function setIsFragile($isFragile)
    {
        $this->isFragile = $isFragile;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Origin
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Origin $origin
     * @return void
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Manufacture
     */
    public function getManufacture()
    {
        return $this->manufacture;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\Manufacture $manufacture
     * @return void
     */
    public function setManufacture($manufacture)
    {
        $this->manufacture = $manufacture;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\ClassificationCodes
     */
    public function getClassificationCodes()
    {
        return $this->classificationCodes;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Item\Product\ClassificationCodes $classificationCodes
     * @return void
     */
    public function setClassificationCodes(ClassificationCodes $classificationCodes)
    {
        $this->classificationCodes = $classificationCodes;
    }

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
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param string $modifiedAt
     * @return void
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
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

    /**
     * @return bool
     */
    public function getIsVirtual()
    {
        return $this->isVirtual;
    }

    /**
     * @param bool $isVirtual
     * @return void
     */
    public function setIsVirtual($isVirtual)
    {
        $this->isVirtual = $isVirtual;
    }

    /**
     * @return bool
     */
    public function isIsPrePackaged()
    {
        return $this->isPrePackaged;
    }

    /**
     * @param bool $isPrePackaged
     * @return void
     */
    public function setIsPrePackaged($isPrePackaged)
    {
        $this->isPrePackaged = $isPrePackaged;
    }

    /**
     * @return bool
     */
    public function isCanRotateVertical()
    {
        return $this->canRotateVertical;
    }

    /**
     * @param bool $canRotateVertical
     * @return void
     */
    public function setCanRotateVertical($canRotateVertical)
    {
        $this->canRotateVertical = $canRotateVertical;
    }
}
