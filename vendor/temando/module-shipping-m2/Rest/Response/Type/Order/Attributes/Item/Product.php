<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Order\Attributes\Item;

use Temando\Shipping\Rest\Response\Type\Generic\Attribute;
use Temando\Shipping\Rest\Response\Type\Generic\Dimensions;
use Temando\Shipping\Rest\Response\Type\Generic\MonetaryValue;
use Temando\Shipping\Rest\Response\Type\Generic\Weight;
use Temando\Shipping\Rest\Response\Type\Order\Attributes\Item\Product\ClassificationCodes;

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
    private $merchantProductId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sku;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $unitOfMeasure;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Dimensions
     */
    private $dimensions;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Weight
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
     * @var string
     */
    private $countryOfOrigin;

    /**
     * @var string
     */
    private $countryOfManufacture;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Order\Attributes\Item\Product\ClassificationCodes
     */
    private $classificationCodes;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Attribute
     */
    private $customAttributes;

    /**
     * @return string
     */
    public function getMerchantProductId()
    {
        return $this->merchantProductId;
    }

    /**
     * @param string $merchantProductId
     * @return void
     */
    public function setMerchantProductId($merchantProductId)
    {
        $this->merchantProductId = $merchantProductId;
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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return void
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getUnitOfMeasure()
    {
        return $this->unitOfMeasure;
    }

    /**
     * @param string $unitOfMeasure
     * @return void
     */
    public function setUnitOfMeasure($unitOfMeasure)
    {
        $this->unitOfMeasure = $unitOfMeasure;
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
    public function getIsPrePackaged()
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
    public function getCanRotateVertical()
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

    /**
     * @return string
     */
    public function getCountryOfOrigin()
    {
        return $this->countryOfOrigin;
    }

    /**
     * @param string $countryOfOrigin
     * @return void
     */
    public function setCountryOfOrigin($countryOfOrigin)
    {
        $this->countryOfOrigin = $countryOfOrigin;
    }

    /**
     * @return string
     */
    public function getCountryOfManufacture()
    {
        return $this->countryOfManufacture;
    }

    /**
     * @param string $countryOfManufacture
     * @return void
     */
    public function setCountryOfManufacture($countryOfManufacture)
    {
        $this->countryOfManufacture = $countryOfManufacture;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Order\Attributes\Item\Product\ClassificationCodes
     */
    public function getClassificationCodes()
    {
        return $this->classificationCodes;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Type\Order\Attributes\Item\Product\ClassificationCodes $classificationCodes
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
