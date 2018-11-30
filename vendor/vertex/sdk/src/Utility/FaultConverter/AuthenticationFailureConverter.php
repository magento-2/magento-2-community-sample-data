<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Utility\FaultConverter;

use SoapFault;
use Vertex\Exception\ApiException\AuthenticationException;
use Vertex\Utility\SoapFaultConverterInterface;

/**
 * Converts a SoapFault into an {@see AuthenticationException}
 *
 * @api
 */
class AuthenticationFailureConverter implements SoapFaultConverterInterface
{
    /**
     * Handle failures to authenticate the Trusted ID or Company Code
     *
     * {@inheritdoc}
     */
    public function convert(SoapFault $fault)
    {
        $trustedIdCompanyCode = 'The Trusted ID could not be resolved, please check your connector configuration. ' .
            'Note that Trusted IDs and Company Codes are case sensitive.';

        if (strpos($fault->getMessage(), $trustedIdCompanyCode) === 0) {
            return new AuthenticationException('The Vertex Trusted ID or Company Code is incorrect', 0, $fault);
        }

        return null;
    }
}
