<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Request;

use Temando\Shipping\Rest\Request\Type\StreamRequestType;

/**
 * Class StreamCreateRequest
 *
 * @package Temando\Shipping\Rest\Request
 */
class StreamCreateRequest implements StreamCreateRequestInterface
{
    /**
     * @var StreamRequestType
     */
    private $stream;

    /**
     * StreamCreateRequest constructor.
     *
     * @param StreamRequestType $stream
     */
    public function __construct(StreamRequestType $stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return string
     */
    public function getRequestBody()
    {
        return json_encode($this->stream);
    }
}
