<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxRegistry;

use Magento\Framework\Registry;

/**
 * Generic framework-registry-based storage for Vertex tax information.
 */
class GenericStorage implements StorageInterface
{
    /** @var Registry */
    private $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $result = $this->registry->registry($key);

        return $result === null ? $default : $result;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Lifetime not supported for registry-based storage.
     */
    public function set($key, $value, $lifetime = 0)
    {
        try {
            $result = true;

            $this->registry->register($key, $value);
        } catch (\RuntimeException $error) {
            $result = false;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key)
    {
        $this->registry->unregister($key);

        return $this->registry->registry($key) === null;
    }
}
