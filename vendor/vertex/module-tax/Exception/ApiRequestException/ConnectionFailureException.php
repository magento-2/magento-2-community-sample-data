<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Exception\ApiRequestException;

use Magento\Framework\Phrase;
use Vertex\Tax\Exception\ApiRequestException;

/**
 * Identifies a failure to connect to a SOAP API
 *
 * May be caused by: Invalid Domain, Failure to Load WSDL
 */
class ConnectionFailureException extends ApiRequestException
{
    /**
     * @param Phrase|null $phrase Defaults to "Unable to connect to remote host"
     * @param \Exception|null $cause
     * @param int $code
     */
    public function __construct(Phrase $phrase = null, \Exception $cause = null)
    {
        if ($phrase === null) {
            $phrase = __('Unable to connect to remote host');
        }
        parent::__construct($phrase, $cause);
    }
}
