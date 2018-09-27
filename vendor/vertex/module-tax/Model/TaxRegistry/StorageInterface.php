<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxRegistry;

/**
 * Tax registry storage for persisting Vertex tax information.
 *
 * The goal of this interface is provide a means for persistent storage when supported, so that requests for tax
 * information over a period of time can be served from local storage instead of APIs.
 */
interface StorageInterface
{
    /**
     * Retrieve an item from storage.
     *
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Write an item to storage.
     *
     * @param string $key
     * @param string $value
     * @param int $lifetime
     * @return boolean
     */
    public function set($key, $value, $lifetime = 0);

    /**
     * Remove an item from storage.
     *
     * @param string $key
     * @return boolean
     */
    public function unsetData($key);
}
