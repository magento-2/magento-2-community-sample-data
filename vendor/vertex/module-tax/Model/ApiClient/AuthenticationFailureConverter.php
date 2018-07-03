<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ApiClient;

use SoapFault;
use Vertex\Tax\Exception\ApiRequestException\AuthenticationException;

/**
 * Converts a SoapFault into an {@see AuthenticationException}
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
        $trustedIdCompanyCode = 'The Trusted ID could not be resolved, please check your connector configuration. '.
            'Note that Trusted IDs and Company Codes are case sensitive.';
        if (strpos($fault->getMessage(), $trustedIdCompanyCode) === 0) {
            return new AuthenticationException(null, $fault);
        }

        return null;
    }
}
