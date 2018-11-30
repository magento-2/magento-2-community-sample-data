<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Exception\ApiException;

use Vertex\Exception\ApiException;

/**
 * Identifies a failure to connect to a SOAP API
 *
 * May be caused by: Invalid Domain, Failure to Load WSDL
 *
 * @api
 */
class ConnectionFailureException extends ApiException
{
    /**
     * @inheritdoc
     */
    public function __construct(
        $message = 'Unable to connect to remote host',
        $code = 0,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
