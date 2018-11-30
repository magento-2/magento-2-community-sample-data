<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Utility;

use Vertex\Utility\FaultConverter\AuthenticationFailureConverter;
use Vertex\Utility\FaultConverter\ConnectionFailureConverter;
use Vertex\Utility\FaultConverter\PooledSoapFaultConverter;

/**
 * Builds the default SoapFaultConverter
 *
 * @api
 */
class SoapFaultConverterBuilder
{
    /**
     * Build the default SoapFaultConverter
     *
     * By default, we pool together all existing SoapFaultConverters
     *
     * @return SoapFaultConverterInterface
     */
    public function build()
    {
        return new PooledSoapFaultConverter(
            [
                new AuthenticationFailureConverter(),
                new ConnectionFailureConverter(),
            ]
        );
    }
}
