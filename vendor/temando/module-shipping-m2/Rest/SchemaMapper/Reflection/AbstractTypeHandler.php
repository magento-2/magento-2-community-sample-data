<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper\Reflection;

/**
 * Temando Type Handler
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
abstract class AbstractTypeHandler implements TypeHandlerInterface
{
    /**
     * @var ReflectionInterface
     */
    private $reflect;

    /**
     * @var string[]
     */
    private $typeMap = [];

    /**
     * AbstractTypeHandler constructor.
     * @param ReflectionInterface $reflect
     */
    public function __construct(ReflectionInterface $reflect)
    {
        $this->reflect = $reflect;
    }

    /**
     * Obtain a property type.
     *
     * @param PropertyHandlerInterface $propertyHandler
     * @param \stdClass $type
     * @param string $property
     * @return string
     */
    public function getPropertyType(PropertyHandlerInterface $propertyHandler, $type, $property)
    {
        $class = get_class($type);

        if (!isset($this->typeMap["$class||$property"])) {
            $propertyType = $this->reflect->getPropertyType($type, $property);
            if (!$propertyType) {
                $getter = $propertyHandler->getter($property);
                $propertyType = $this->reflect->getReturnValueType($type, $getter);
            }

            $this->typeMap["$class||$property"] = $propertyType;
        }

        return $this->typeMap["$class||$property"];
    }

    /**
     * Obtain a property meta type.
     *
     * @param PropertyHandlerInterface $propertyHandler
     * @param \stdClass $type
     * @param string $property
     * @return string
     */
    public function getPropertyMetaType(PropertyHandlerInterface $propertyHandler, $type, $property)
    {
        $propertyType = $this->getPropertyType($propertyHandler, $type, $property);

        // object or scalar
        if (!preg_match('/([\w\\\\]+)\[\]$/', $propertyType, $matches)) {
            if (class_exists($propertyType) || interface_exists($propertyType)) {
                return self::META_TYPE_OBJECT;
            } else {
                return self::META_TYPE_SCALAR;
            }
        }

        $scalarTypes = [
            self::TYPE_INTEGER,
            self::TYPE_FLOAT,
            self::TYPE_STRING,
            self::TYPE_BOOLEAN,
            self::TYPE_SHORT_INTEGER,
            self::TYPE_SHORT_BOOLEAN
        ];

        // array type
        if (in_array($matches[1], $scalarTypes)) {
            // array of scalar
            $propertyType = self::META_TYPE_ARRAY;
        } else {
            // array of type
            $propertyType = self::META_TYPE_OBJECT_ARRAY;
        }

        return $propertyType;
    }
}
