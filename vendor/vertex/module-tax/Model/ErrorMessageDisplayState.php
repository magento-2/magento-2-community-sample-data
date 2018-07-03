<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

/**
 * Used to decide when to enable the "Vertex cannot calculate taxes" error message
 */
class ErrorMessageDisplayState
{
    /** @var bool */
    private $enabled;

    /**
     * @param bool $enabled
     */
    public function __construct($enabled = false)
    {
        $this->enabled = $enabled;
    }

    /**
     * Determine if the failure to calculate taxes message is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Enable the failure to calculate taxes message
     *
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disable the failure to calculate taxes message
     *
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }
}
