<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type\Order\OrderItem;

use Temando\Shipping\Rest\Request\Type\EmptyFilterableInterface;
use Temando\Shipping\Rest\Request\Type\AttributeFilter;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeAttribute;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeInterface;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeProcessor;

/**
 * Temando API Order Item Classification Codes Attributes Request Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ClassificationCodes implements \JsonSerializable, EmptyFilterableInterface, ExtensibleTypeInterface
{
    /**
     * @var string
     */
    private $eccn;

    /**
     * @var string
     */
    private $scheduleBinfo;

    /**
     * @var string
     */
    private $hsCode;

    /**
     * @var ExtensibleTypeAttribute[]
     */
    private $additionalAttributes = [];

    /**
     * ClassificationCodes constructor.
     * @param string $eccn
     * @param string $scheduleBinfo
     * @param string $hsCode
     */
    public function __construct($eccn, $scheduleBinfo, $hsCode)
    {
        $this->eccn = $eccn;
        $this->scheduleBinfo = $scheduleBinfo;
        $this->hsCode = $hsCode;
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
        $codes = [
            'eccn' => $this->eccn,
            'scheduleBinfo' => $this->scheduleBinfo,
            'hsCode' => $this->hsCode,
        ];

        foreach ($this->additionalAttributes as $additionalAttribute) {
            $codes = ExtensibleTypeProcessor::addAttribute($codes, $additionalAttribute);
        }
        $codes = AttributeFilter::notEmpty($codes);

        return $codes;
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
