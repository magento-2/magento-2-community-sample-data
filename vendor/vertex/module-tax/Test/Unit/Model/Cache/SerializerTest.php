<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Cache;

use Vertex\Tax\Model\Cache\Serializer;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Test cache storage serializer functionality.
 */
class SerializerTest extends TestCase
{
    /** @var Serializer */
    private $serializer;

    /**
     * Perform test setup.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->serializer = $this->getObject(Serializer::class);
    }

    /**
     * Test that string data can be handled by the serializer with integrity.
     *
     * @covers \Vertex\Tax\Model\Cache\Serializer::serialize()
     * @covers \Vertex\Tax\Model\Cache\Serializer::unserialize()
     */
    public function testStringType()
    {
        $expectedValue = 'test';
        $output = $this->serializer->serialize($expectedValue);
        $actualResult = $this->serializer->unserialize($output);

        $this->assertEquals($expectedValue, $actualResult);
    }

    /**
     * Test that double type data can be handled by the serializer with integrity.
     *
     * @covers \Vertex\Tax\Model\Cache\Serializer::serialize()
     * @covers \Vertex\Tax\Model\Cache\Serializer::unserialize()
     */
    public function testDoubleType()
    {
        $expectedValue = 65.18;
        $output = $this->serializer->serialize($expectedValue);
        $actualResult = $this->serializer->unserialize($output);

        $this->assertEquals($expectedValue, $actualResult);
    }

    /**
     * Test that integer data can be handled by the serializer with integrity.
     *
     * @covers \Vertex\Tax\Model\Cache\Serializer::serialize()
     * @covers \Vertex\Tax\Model\Cache\Serializer::unserialize()
     */
    public function testIntegerType()
    {
        $expectedValue = 100;
        $output = $this->serializer->serialize($expectedValue);
        $actualResult = $this->serializer->unserialize($output);

        $this->assertEquals($expectedValue, $actualResult);
    }

    /**
     * Test that boolean data can be handled by the serializer with integrity.
     *
     * @covers \Vertex\Tax\Model\Cache\Serializer::serialize()
     * @covers \Vertex\Tax\Model\Cache\Serializer::unserialize()
     */
    public function testBooleanType()
    {
        $expectedValue = false;
        $output = $this->serializer->serialize($expectedValue);
        $actualResult = $this->serializer->unserialize($output);

        $this->assertEquals($expectedValue, $actualResult);
    }

    /**
     * Test that array data can be handled by the serializer with integrity.
     *
     * @covers \Vertex\Tax\Model\Cache\Serializer::serialize()
     * @covers \Vertex\Tax\Model\Cache\Serializer::unserialize()
     */
    public function testArrayType()
    {
        $expectedValue = ['test'];
        $output = $this->serializer->serialize($expectedValue);
        $actualResult = $this->serializer->unserialize($output);

        $this->assertEquals('array', gettype($actualResult));
        $this->assertTrue(count($actualResult) === 1);
        $this->assertEquals($expectedValue[0], $actualResult[0]);
    }

    /**
     * Test that boolean data can be handled by the serializer with integrity.
     *
     * @covers \Vertex\Tax\Model\Cache\Serializer::serialize()
     * @covers \Vertex\Tax\Model\Cache\Serializer::unserialize()
     */
    public function testNullType()
    {
        $expectedValue = null;
        $output = $this->serializer->serialize($expectedValue);
        $actualResult = $this->serializer->unserialize($output);

        $this->assertEquals($expectedValue, $actualResult);
    }

    /**
     * Test that unsupported types will throw an exception.
     *
     * @dataProvider provideUnsupportedInputs
     * @param mixed $input
     * @covers \Vertex\Tax\Model\Cache\Serializer::serialize()
     * @covers \Vertex\Tax\Model\Cache\Serializer::unserialize()
     */
    public function testUnsupportedInputType($input)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->serializer->serialize($input);
    }

    /**
     * Test that circular references within an array cannot be accepted.
     *
     * @covers \Vertex\Tax\Model\Cache\Serializer::serialize()
     * @covers \Vertex\Tax\Model\Cache\Serializer::unserialize()
     */
    public function testArrayRecursion()
    {
        // For local environment tests in which XDebug is enabled.
        ini_set('xdebug.max_nesting_level', Serializer::MAX_ARRAY_DEPTH + 1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Serializable array depth cannot exceed %d', Serializer::MAX_ARRAY_DEPTH)
        );

        $input = ['test'];
        $input['reference'] = &$input;

        $this->serializer->serialize($input);
    }

    /**
     * Provide various inputs that should not be supported by serialization
     *
     * @return array
     */
    public function provideUnsupportedInputs()
    {
        return [
            [$this->getMockBuilder('NonStandardObject')],
            [['key' => $this->getMockBuilder('NonStandardObject')],],
            [['key' => ['otherKey' => $this->getMockBuilder('NonStandardObject')]]],
        ];
    }
}
