<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Exception\ApiException;

use Vertex\Exception\ApiException;

/**
 * Identifies a failure to resolve the Trusted ID / Company Code combination
 *
 * @api
 */
class AuthenticationException extends ApiException
{
    /**
     * @inheritdoc
     */
    public function __construct(
        $message = 'The Vertex Trusted ID or Company Code is incorrect',
        $code = 0,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
