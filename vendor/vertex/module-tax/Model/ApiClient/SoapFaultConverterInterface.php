<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ApiClient;

use SoapFault;
use Vertex\Tax\Exception\ApiRequestException;

/**
 * Responsible for converting SoapFaults encountered while communicating with Vertex to reasonable Exceptions
 */
interface SoapFaultConverterInterface
{
    /**
     * Convert a {@see SoapFault} into an {@see ApiRequestException}
     *
     * @param SoapFault $fault
     * @return ApiRequestException|null
     */
    public function convert(SoapFault $fault);
}
