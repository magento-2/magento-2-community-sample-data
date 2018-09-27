<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type\Order;

use Temando\Shipping\Rest\Request\Type\AttributeFilter;
use Temando\Shipping\Rest\Request\Type\EmptyFilterableInterface;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeAttribute;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeInterface;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeProcessor;

/**
 * Temando API Order Meta Collection Point Search Request Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CollectionPointSearch implements \JsonSerializable, EmptyFilterableInterface, ExtensibleTypeInterface
{
    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var ExtensibleTypeAttribute[]
     */
    private $additionalAttributes = [];

    /**
     * CollectionPointSearch constructor.
     * @param string $postalCode
     * @param string $countryCode
     */
    public function __construct($postalCode, $countryCode)
    {
        $this->postalCode = $postalCode;
        $this->countryCode = $countryCode;
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
        $collectionPointSearch = [
            'address' => [
                'postalCode' => $this->postalCode,
                'countryCode' => $this->countryCode,
            ]
        ];

        foreach ($this->additionalAttributes as $additionalAttribute) {
            $collectionPointSearch = ExtensibleTypeProcessor::addAttribute(
                $collectionPointSearch,
                $additionalAttribute
            );
        }
        $collectionPointSearch = AttributeFilter::notEmpty($collectionPointSearch);

        return $collectionPointSearch;
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
