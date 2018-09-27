<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request;

/**
 * Temando API Get Item Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class StreamEventItemRequest implements StreamEventItemRequestInterface
{
    /**
     * @var string
     */
    private $streamId;

    /**
     * @var string
     */
    private $entityId;

    /**
     * StreamEventItemRequest constructor.
     * @param string $streamId
     * @param string $entityId
     */
    public function __construct($streamId, $entityId)
    {
        $this->streamId = $streamId;
        $this->entityId = $entityId;
    }

    /**
     * @return string[]
     */
    public function getPathParams()
    {
        return [
            $this->streamId,
            $this->entityId,
        ];
    }
}
