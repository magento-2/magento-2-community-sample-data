<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Exception;

/**
 * Temando Webservice Response Exception
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class HttpResponseException extends HttpException
{
    /**
     * @var string[]
     */
    private $responseHeaders;

    /**
     * HttpResponseException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     * @param string $responseHeaders
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null, $responseHeaders = '')
    {
        $this->responseHeaders = $responseHeaders;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return \string[]
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }
}
