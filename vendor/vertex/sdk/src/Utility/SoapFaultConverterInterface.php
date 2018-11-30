<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Utility;

use SoapFault;
use Vertex\Exception\ApiException;

/**
 * Responsible for converting SoapFaults encountered while communicating with Vertex to reasonable Exceptions
 *
 * @api
 */
interface SoapFaultConverterInterface
{
    /**
     * Convert a {@see SoapFault} into an {@see ApiRequestException}
     *
     * @param SoapFault $fault
     * @return ApiException|null
     */
    public function convert(SoapFault $fault);
}
