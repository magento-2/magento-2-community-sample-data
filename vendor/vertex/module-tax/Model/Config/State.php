<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Config;

use Vertex\Tax\Model\Config;

/**
 * Provides Vertex integration enablement state.
 *
 * This is a wrapper for the underlying config model. It is designed to add an additional layer of control to the
 * enabled state of Vertex. Using this model, it is possible to activate select features in a specific context while
 * Vertex remains inactive.
 */
class State
{
    /** @var Config */
    private $config;

    /** @var bool */
    private $forceActive;

    /**
     * @param Config $config
     * @param boolean $forceActive
     */
    public function __construct(Config $config, $forceActive = false)
    {
        $this->config = $config;
        $this->forceActive = $forceActive;
    }

    /**
     * Determine whether the Vertex integration is active.
     *
     * @param string|null $store
     * @return bool
     */
    public function isActive($store = null)
    {
        return $this->forceActive || $this->config->isVertexActive($store);
    }
}
