<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper;

use Temando\Shipping\Rest\SchemaMapper\Reflection\PropertyHandlerInterface;
use Temando\Shipping\Rest\SchemaMapper\Reflection\TypeHandlerInterface;

/**
 * Temando Data Parser
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
abstract class AbstractParser implements ParserInterface
{
    /**
     * @var PropertyHandlerInterface
     */
    private $propertyHandler;

    /**
     * @var TypeHandlerInterface
     */
    private $typeHandler;

    /**
     * AbstractParser constructor.
     * @param PropertyHandlerInterface $propertyHandler
     * @param TypeHandlerInterface $typeHandler
     */
    public function __construct(
        PropertyHandlerInterface $propertyHandler,
        TypeHandlerInterface $typeHandler
    ) {
        $this->propertyHandler = $propertyHandler;
        $this->typeHandler = $typeHandler;
    }

    /**
     * Convert the string representation of a given type. The input data format
     * (xml, json, etc.) is handled by the concrete parser class.
     *
     * @param string $data The data to be parsed
     * @param string $type The type (interface) to map the data to
     * @return object The object with populated properties
     */
    abstract public function parse($data, $type);

    /**
     * Copy the properties to an object of the given type.
     *
     * @param mixed[] $properties Associated array of property keys and values.
     * @param string $type The type of the target object.
     * @return object The target object with all properties set.
     */
    public function parseProperties(array $properties, $type)
    {
        $dataObj = $this->typeHandler->create($type);

        // named type
        foreach ($properties as $key => $value) {
            $subType = $this->typeHandler->getPropertyType($this->propertyHandler, $dataObj, $key);
            if (!$subType) {
                continue;
            }

            $subMetaType = $this->typeHandler->getPropertyMetaType($this->propertyHandler, $dataObj, $key);
            if ($subMetaType == TypeHandlerInterface::META_TYPE_OBJECT) {
                $value = $this->parseProperties($value, $subType);
            } elseif ($subMetaType == TypeHandlerInterface::META_TYPE_OBJECT_ARRAY) {
                $subType = rtrim($subType, '[]');

                $types = [];
                foreach ($value as $item) {
                    $types[]= $this->parseProperties($item, $subType);
                }

                $value = $types;
            }

            // set value
            $setter = $this->propertyHandler->setter($key);
            call_user_func([$dataObj, $setter], $value);
        }

        return $dataObj;
    }
}
