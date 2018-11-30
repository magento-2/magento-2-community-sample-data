<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\TaxRegistry;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\Cache\FrontendInterface;
use Vertex\Tax\Model\Cache\Serializer;
use Vertex\Tax\Model\TaxRegistry\CacheStorage;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Test Vertex tax registry cache storage.
 */
class CacheStorageTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|FrontendInterface */
    private $cacheFrontendMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|StateInterface */
    private $cacheStateMock;

    /** @var CacheStorage */
    private $cacheStorage;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Serializer */
    private $serializerMock;

    /**
     * Perform test setup.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->cacheFrontendMock = $this->createMock(FrontendInterface::class);
        $this->cacheStateMock = $this->createMock(StateInterface::class);
        $this->serializerMock = $this->createMock(Serializer::class);

        $this->cacheStateMock->method('isEnabled')
            ->willReturn(true);

        $this->cacheStorage = $this->getObject(
            CacheStorage::class,
            [
                'cache' => $this->cacheFrontendMock,
                'cacheState' => $this->cacheStateMock,
                'serializer' => $this->serializerMock,
            ]
        );
    }

    /**
     * Test that the absence of cached data can be handled.
     *
     * @covers \Vertex\Tax\Model\TaxRegistry\CacheStorage::get()
     */
    public function testMissingCacheEntryRetrievalResolvesToNull()
    {
        $this->cacheFrontendMock->method('load')
            ->willReturn(false);

        $this->serializerMock->expects($this->never())
            ->method('unserialize');

        $this->assertNull(
            $this->cacheStorage->get('non_existent_key')
        );
    }

    /**
     * Test that the absence of cached data will return a specified default value.
     *
     * @covers \Vertex\Tax\Model\TaxRegistry\CacheStorage::get()
     */
    public function testMissingCacheEntryRetrievalResolvesToFallback()
    {
        $expectedValue = 'default_value';

        $this->cacheFrontendMock->method('load')
            ->willReturn(false);

        $actualValue = $this->cacheStorage->get('non_existent_key', $expectedValue);

        $this->assertEquals($expectedValue, $actualValue);
    }

    /**
     * Test that scalar data can be retrieved from storage with type integrity.
     *
     * @param array $data
     * @dataProvider provideScalarData
     * @covers \Vertex\Tax\Model\TaxRegistry\CacheStorage::get()
     */
    public function testGetScalarData(array $data)
    {
        $expectedValues = array_values($data);

        $this->cacheFrontendMock->expects($this->exactly(count($expectedValues)))
            ->method('load');

        $this->serializerMock->expects($this->exactly(count($expectedValues)))
            ->method('unserialize')
            ->willReturnOnConsecutiveCalls(...$expectedValues);

        foreach ($data as $type => $expectedValue) {
            $storageKey = 'test_type_' . $type;
            $actualResult = $this->cacheStorage->get($storageKey);

            $this->assertEquals(gettype($actualResult), $type);
            $this->assertEquals($expectedValue, $actualResult);
        }
    }

    /**
     * Test that array data can be retrieved from storage with type integrity.
     *
     * @param array $data
     * @dataProvider provideScalarData
     * @covers \Vertex\Tax\Model\TaxRegistry\CacheStorage::get()
     */
    public function testGetArrayData(array $data)
    {
        $this->cacheFrontendMock->method('load')
            ->willReturn($data);

        $this->serializerMock->expects($this->once())
            ->method('unserialize')
            ->willReturn($data);

        $storageKey = 'test_type_array';
        $actualResult = $this->cacheStorage->get($storageKey);

        $this->assertEquals(gettype($actualResult), 'array');
        $this->assertEquals(serialize($data), serialize($actualResult));
    }

    /**
     * Test that scalar data can be written to storage with type integrity.
     *
     * @param array $data
     * @dataProvider provideScalarData
     * @covers \Vertex\Tax\Model\TaxRegistry\CacheStorage::set()
     */
    public function testSetScalarData(array $data)
    {
        $expectedValues = array_values($data);

        $this->cacheFrontendMock->method('save')
            ->willReturn(true);

        $this->serializerMock->expects($this->exactly(count($expectedValues)))
            ->method('serialize');

        foreach ($data as $type => $expectedValue) {
            $storageKey = 'test_type_' . $type;

            $this->assertTrue(
                $this->cacheStorage->set($storageKey, $expectedValue)
            );
        }
    }

    /**
     * Test that array data can be written to storage with type integrity.
     *
     * @param array $data
     * @dataProvider provideScalarData
     * @covers \Vertex\Tax\Model\TaxRegistry\CacheStorage::set()
     */
    public function testSetArrayData(array $data)
    {
        $this->cacheFrontendMock->method('save')
            ->willReturn(true);

        $this->serializerMock->expects($this->once())
            ->method('serialize')
            ->willReturn(serialize($data));

        $storageKey = 'test_type_array';

        $this->assertTrue(
            $this->cacheStorage->set($storageKey, $data)
        );
    }

    /**
     * Data provider of scalar data for tax registry storage tests.
     *
     * @return array
     */
    public function provideScalarData()
    {
        return [
            'data' => [
                [
                    'boolean' => false,
                    'integer' => 255,
                    'double' => 515.11,
                    'string' => 'test string type',
                ],
            ],
        ];
    }

    /**
     * Data provider of array data for tax registry storage tests.
     *
     * @return array
     */
    public function provideArrayData()
    {
        return [
            'data' => [
                [
                    'id' => 918,
                    'key' => 'identifier',
                    'value' => 'test value',
                ],
            ],
        ];
    }
}
