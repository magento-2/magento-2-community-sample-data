<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type\Order\Experience;

use Temando\Shipping\Rest\Request\Type\EmptyFilterableInterface;
use Temando\Shipping\Rest\Request\Type\AttributeFilter;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeAttribute;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeInterface;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeProcessor;

/**
 * Temando API Order Included Experience Description Attributes Request Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Description implements \JsonSerializable, EmptyFilterableInterface, ExtensibleTypeInterface
{
    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $text;

    /**
     * @var ExtensibleTypeAttribute[]
     */
    private $additionalAttributes = [];

    /**
     * Description constructor.
     * @param string $language
     * @param string $text
     */
    public function __construct($language, $text)
    {
        $this->language = $language;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return void
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
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
        $description = [
            'language' => $this->language,
            'text' => $this->text,
        ];

        foreach ($this->additionalAttributes as $additionalAttribute) {
            $description = ExtensibleTypeProcessor::addAttribute($description, $additionalAttribute);
        }
        $description = AttributeFilter::notEmpty($description);

        return $description;
    }

    /**
     * Check if a complete request type should be treated as empty.
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
