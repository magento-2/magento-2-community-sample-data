<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var ObjectManager */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        parent::setUp();
    }

    protected function getObject($className, $arguments = [])
    {
        return $this->objectManager->getObject($className, $arguments);
    }

    protected function getCollection($className, $data = [])
    {
        return $this->objectManager->getCollectionMock($className, $data);
    }

    public function invokeInaccessibleMethod(&$object, $methodName)
    {
        $parameters = array_slice(func_get_args(), 2);
        $reflection = new \ReflectionMethod(get_class($object), $methodName);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $parameters);
    }

    public function setInaccessibleProperty(&$object, $propertyName, $value)
    {
        $reflection = new \ReflectionProperty(get_class($object), $propertyName);
        $reflection->setAccessible(true);

        $reflection->setValue($object, $value);
    }

    public function setProtectedProperty(&$object, $propertyName, $value)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
