<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type;

/**
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ExtensibleTypeAttribute implements EmptyFilterableInterface
{
    /**
     * @var string
     */
    private $attributeId;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string[]
     */
    private $dataPath;

    /**
     * ExtensibleTypeAttribute constructor.
     * @param string $attributeId
     * @param string $value
     * @param \string[] $dataPath
     */
    public function __construct($attributeId, $value, array $dataPath)
    {
        $this->attributeId = $attributeId;
        $this->dataPath = $dataPath;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \string[]
     */
    public function getDataPath()
    {
        return $this->dataPath;
    }

    /**
     * @param \string[] $dataPath
     * @return void
     */
    public function setDataPath(array $dataPath)
    {
        $this->dataPath = $dataPath;
    }

    /**
     * Check if any properties are set.
     *
     * @return bool
     */
    public function isEmpty()
    {
        $properties = [
            'value' => $this->value,
        ];
        $properties = AttributeFilter::notEmpty($properties);
        return empty($properties);
    }
}
