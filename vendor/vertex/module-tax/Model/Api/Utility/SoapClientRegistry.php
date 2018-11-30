<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Utility;

/**
 * Keeps a record of the last created SoapClient
 */
class SoapClientRegistry
{
    /** @var \SoapClient|null */
    private $lastClient;

    /**
     * Retrieve the last {@see \SoapClient} created by {@see SoapClientFactory}
     *
     * @return \SoapClient|null
     */
    public function getLastClient()
    {
        return $this->lastClient;
    }

    /**
     * Set the last {@see \SoapClient} created by {@see SoapClientFactory}
     *
     * @param \SoapClient $client
     * @return SoapClientRegistry
     */
    public function setLastClient(\SoapClient $client)
    {
        $this->lastClient = $client;
        return $this;
    }
}
