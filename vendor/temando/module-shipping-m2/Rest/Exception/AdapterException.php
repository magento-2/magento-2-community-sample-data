<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Exception;

use Temando\Shipping\Rest\Response\Errors;
use Temando\Shipping\Rest\Response\Type\ErrorResponseType;

/**
 * Temando REST Adapter Exception – parsed Http Exception
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AdapterException extends RestException
{
    /**
     * @param \Exception $cause
     * @return static
     */
    public static function create(\Exception $cause)
    {
        $message = 'API connection failed';

        return new static($message, $cause->getCode(), $cause);
    }

    /**
     * @param Errors $errors
     * @param \Exception|null $cause
     * @return static
     */
    public static function errorResponse(Errors $errors, \Exception $cause = null)
    {
        $messages = [];

        if ($errors->getErrors() !== null) {
            $messages = array_map(function (ErrorResponseType $error) {
                return sprintf('%s: %s', $error->getCode(), $error->getTitle());
            }, $errors->getErrors());
        }

        $messages = implode(', ', $messages);

        if ($cause !== null) {
            return new static($messages, $cause->getCode(), $cause);
        }

        return new static($messages);
    }
}
