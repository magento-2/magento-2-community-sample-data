<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type;

/**
 * StreamRequestType
 *
 * @package  Temando\Shipping\Rest
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
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
