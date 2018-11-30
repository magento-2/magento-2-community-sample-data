<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\TaxRegistry;

use Magento\Framework\App\Cache\StateInterface;
use Vertex\Tax\Model\Cache\Type as CacheType;
use Vertex\Tax\Model\TaxRegistry\CacheStorage;
use Vertex\Tax\Test\Integration\TestCase;

/**
 * Ensure that cache storage persists data across requests.
 * @magentoAppArea frontend
 */
class CacheStoragePersistenceTest extends TestCase
{
    /** @var StateInterface */
    private $cacheState;

    /** @var CacheStorage */
    private $cacheStorage;

    /**
     * Perform test setup.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->cacheState = $this->getObject(StateInterface::class);
        $this->cacheStorage = $this->getObject(CacheStorage::class);
    }

    /**
     * Test that cache storage can unset its data.
     */
    public function testSuccessfulCacheUnset()
    {
        $this->cacheState->setEnabled(CacheType::TYPE_IDENTIFIER, true);
        $this->assertTrue($this->cacheState->isEnabled(CacheType::TYPE_IDENTIFIER));

        $this->assertTrue($this->cacheStorage->set('key_to_unset', 'value_to_unset'));
        $this->assertTrue($this->cacheStorage->unsetData('key_to_unset'));
        $this->assertNull($this->cacheStorage->get('key_to_unset'));
    }

    /**
     * Test that cache storage succeeds when in fallback mode.
     */
    public function testGenericPersistenceUnderCacheDisablement()
    {
        $this->cacheState->setEnabled(CacheType::TYPE_IDENTIFIER, false);
        $this->assertFalse($this->cacheState->isEnabled(CacheType::TYPE_IDENTIFIER));

        $expectedResult = 'test_value';

        $this->cacheStorage->set('test_key', $expectedResult);
        $actualResult = $this->cacheStorage->get('test_key');

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Test that cache storage succeeds when enabled.
     */
    public function testPersistenceUnderCacheEnablement()
    {
        $this->cacheState->setEnabled(CacheType::TYPE_IDENTIFIER, true);
        $this->assertTrue($this->cacheState->isEnabled(CacheType::TYPE_IDENTIFIER));

        $expectedResult = 'test_value2';

        $this->cacheStorage->set('test_key2', $expectedResult, 300);
        $actualResult = $this->cacheStorage->get('test_key2');

        $this->assertEquals($expectedResult, $actualResult);
    }
}
