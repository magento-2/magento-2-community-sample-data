<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type;

/**
 * Class StreamRequestType
 *
 * @package Temando\Shipping\Rest\Request\Type
 */
class StreamRequestType implements \JsonSerializable
{
    /**
     * @var string
     */
    private $streamId;

    /**
     * StreamRequestType constructor.
     * @param string $streamId
     */
    public function __construct($streamId)
    {
        $this->streamId = $streamId;
    }

    /**
     * @return string[][]
     */
    public function jsonSerialize()
    {
        return [
            'data' => [
                'type' => 'stream',
                'id' => $this->streamId,
            ]
        ];
    }
}
