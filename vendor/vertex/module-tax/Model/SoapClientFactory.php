<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

/**
 * Factory for creating \SoapClients
 *
 * This is necessary b/c Magento cannot generate factories for non-magento objects
 */
class SoapClientFactory
{
    /**
     * Create a SoapClient
     *
     * @param mixed $wsdl
     * @param array $options
     * @return \SoapClient
     */
    public function create($wsdl, array $options = [])
    {
        // Magento does not support factories for non-magento objects
        return new \SoapClient($wsdl, $options);
    }
}
