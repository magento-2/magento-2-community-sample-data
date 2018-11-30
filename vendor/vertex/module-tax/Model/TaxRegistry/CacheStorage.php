<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxRegistry;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\Cache\FrontendInterface;
use Vertex\Tax\Model\Cache\Type as CacheType;
use Vertex\Tax\Model\Cache\Serializer;

/**
 * Persistent storage for Vertex tax information.
 */
class CacheStorage extends GenericStorage
{
    const CACHE_ID_PREFIX = 'VERTEX_';

    /** @var FrontendInterface */
    private $cache;

    /** @var bool */
    private $enabled;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param FrontendInterface $cache
     */
    public function __construct(
        FrontendInterface $cache,
        StateInterface $cacheState,
        Serializer $serializer
    ) {
        $this->cache = $cache;
        $this->enabled = $cacheState->isEnabled(CacheType::TYPE_IDENTIFIER);
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (!$this->enabled) {
            return parent::get($key, $default);
        }

        $result = $this->cache->load($this->getCacheId($key));

        return $result === false ? $default : $this->unserialize($result);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $lifetime = 0)
    {
        if (!$this->enabled) {
            return parent::set($key, $value, $lifetime);
        }

        return $this->cache->save($this->serialize($value), $this->getCacheId($key), [], $lifetime);
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key)
    {
        if (!$this->enabled) {
            return parent::unsetData($key);
        }

        $this->cache->remove($key);

        return $this->cache->load($key) === null;
    }

    /**
     * Generate a cache identifier from the given input.
     *
     * @param string $input
     * @return string
     */
    private function getCacheId($input)
    {
        return self::CACHE_ID_PREFIX . sha1($input);
    }

    /**
     * Serialize the given data.
     *
     * @param mixed $data
     * @return string
     */
    private function serialize($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * Unserialize the given data.
     *
     * @param string $data
     * @return mixed
     */
    private function unserialize($data)
    {
        return $this->serializer->unserialize($data);
    }
}
