<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ApiClient;

use \SoapFault;
use Vertex\Tax\Exception\ApiRequestException\ConnectionFailureException;

/**
 * Converts {@see SoapFault} to {@see ConnectionFailureException}s
 */
class ConnectionFailureConverter implements SoapFaultConverterInterface
{
    /**
     * Handle failures to connect to the remote server or load the WSDL
     *
     * {@inheritdoc}
     */
    public function convert(SoapFault $fault)
    {
        $couldNotLoadWsdlString = 'SOAP-ERROR: Parsing WSDL: Couldn\'t load';
        if (strpos($fault->getMessage(), $couldNotLoadWsdlString) === 0) {
            return new ConnectionFailureException(null, $fault);
        }

        return null;
    }
}
