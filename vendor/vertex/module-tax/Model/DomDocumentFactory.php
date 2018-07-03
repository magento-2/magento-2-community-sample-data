<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

/**
 * Factory for a DOMDocument
 */
class DomDocumentFactory
{
    /**
     * Create a DOMDocument
     *
     * @return \DOMDocument
     */
    public function create()
    {
        return new \DOMDocument();
    }
}
