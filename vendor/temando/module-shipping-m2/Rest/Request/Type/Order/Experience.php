<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type\Order;

use Temando\Shipping\Rest\Request\Type\EmptyFilterableInterface;
use Temando\Shipping\Rest\Request\Type\AttributeFilter;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeAttribute;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeInterface;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeProcessor;
use Temando\Shipping\Rest\Request\Type\Generic\MonetaryValue;
use Temando\Shipping\Rest\Request\Type\Order\Experience\Description;

/**
 * Temando API Order Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Experience implements \JsonSerializable, EmptyFilterableInterface, ExtensibleTypeInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var MonetaryValue
     */
    private $cost;

    /**
     * @var Description
     */
    private $description;

    /**
     * @var ExtensibleTypeAttribute[]
     */
    private $additionalAttributes = [];

    /**
     * Experience constructor.
     * @param string $code
     * @param MonetaryValue $cost
     * @param Description $description
     */
    public function __construct($code, MonetaryValue $cost, Description $description)
    {
        $this->code = $code;
        $this->cost = $cost;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return void
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return MonetaryValue
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param MonetaryValue $cost
     * @return void
     */
    public function setCost(MonetaryValue $cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return Description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param Description $description
     * @return void
     */
    public function setDescription(Description $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->description;
    }

    /**
     * Add further dynamic request attributes to the request type.
     *
     * @param ExtensibleTypeAttribute $attribute
     * @return void
     */
    public function addAdditionalAttribute(ExtensibleTypeAttribute $attribute)
    {
        $this->additionalAttributes[$attribute->getAttributeId()] = $attribute;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize()
    {
        $experience = [
            'code' => $this->code,
            'cost' => $this->cost,
            'description' => $this->description,
        ];

        foreach ($this->additionalAttributes as $additionalAttribute) {
            $experience = ExtensibleTypeProcessor::addAttribute($experience, $additionalAttribute);
        }
        $experience = AttributeFilter::notEmpty($experience);

        return $experience;
    }

    /**
     * Check if any properties are set.
     *
     * @return bool
     */
    public function isEmpty()
    {
        $properties = get_object_vars($this);
        $properties = AttributeFilter::notEmpty($properties);
        return empty($properties);
    }
}
