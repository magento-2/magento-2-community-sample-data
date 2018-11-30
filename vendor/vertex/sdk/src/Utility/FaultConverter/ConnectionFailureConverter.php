<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Utility\FaultConverter;

use SoapFault;
use Vertex\Exception\ApiException\ConnectionFailureException;
use Vertex\Utility\SoapFaultConverterInterface;

/**
 * Converts {@see SoapFault} to {@see ConnectionFailureException}s
 *
 * @api
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
            return new ConnectionFailureException('Unable to connect to remote host', 0, $fault);
        }

        return null;
    }
}
