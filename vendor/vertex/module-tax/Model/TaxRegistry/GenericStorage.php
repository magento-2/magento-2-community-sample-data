<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxRegistry;

/**
 * Generic framework-registry-based storage for Vertex tax information.
 */
class GenericStorage implements StorageInterface
{
    /**
     * Internal storage for tax data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Lifetime not supported for generic storage.
     */
    public function set($key, $value, $lifetime = 0)
    {
        if (isset($this->data[$key])) {
            return false;
        }

        $this->data[$key] = $value;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);

            return true;
        }

        return false;
    }
}
