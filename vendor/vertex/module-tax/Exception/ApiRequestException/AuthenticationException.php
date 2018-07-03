<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Exception\ApiRequestException;

use Magento\Framework\Phrase;
use Vertex\Tax\Exception\ApiRequestException;

/**
 * Identifies a failure to resolve the Trusted ID / Company Code combination
 */
class AuthenticationException extends ApiRequestException
{
    /**
     * @param Phrase|null $phrase Defaults to "Unable to connect to remote host"
     * @param \Exception|null $cause
     * @param int $code
     */
    public function __construct(Phrase $phrase = null, \Exception $cause = null)
    {
        if ($phrase === null) {
            $phrase = __('The Vertex Trusted ID or Company Code is incorrect');
        }
        parent::__construct($phrase, $cause);
    }
}
