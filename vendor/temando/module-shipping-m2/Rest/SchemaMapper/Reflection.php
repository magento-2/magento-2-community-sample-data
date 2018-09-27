<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper;

use Magento\Framework\Reflection\TypeProcessor;
use Temando\Shipping\Rest\SchemaMapper\Reflection\ReflectionInterface;

/**
 * Temando API Type Reflection Utility
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Reflection implements ReflectionInterface
{
    /**
     * @var TypeProcessor
     */
    private $typeProcessor;

    /**
     * Reflection constructor.
     * @param TypeProcessor $typeProcessor
     */
    public function __construct(TypeProcessor $typeProcessor)
    {
        $this->typeProcessor = $typeProcessor;
    }

    /**
     * @param \stdClass $type
     * @param string $property
     * @return string
     */
    public function getPropertyType($type, $property)
    {
        try {
            $reflectionClass = new \Zend\Code\Reflection\ClassReflection($type);
            $tag = $reflectionClass->getProperty($property)->getDocBlock()->getTag('var');
        } catch (\ReflectionException $e) {
            return '';
        }

        if ($tag instanceof \Zend\Code\Reflection\DocBlock\Tag\PhpDocTypedTagInterface) {
            $propertyTypes = $tag->getTypes();
            return current($propertyTypes);
        } elseif ($tag instanceof \Zend\Code\Reflection\DocBlock\Tag\GenericTag) {
            return $tag->getContent();
        }

        return '';
    }

    /**
     * @param \stdClass $type
     * @param string $getter
     * @return mixed
     */
    public function getReturnValueType($type, $getter)
    {
        try {
            $reflectionMethod = new \Zend\Code\Reflection\MethodReflection($type, $getter);
            $typeInfo = $this->typeProcessor->getGetterReturnType($reflectionMethod);
        } catch (\ReflectionException $e) {
            return null;
        }

        return $typeInfo['type'];
    }
}
