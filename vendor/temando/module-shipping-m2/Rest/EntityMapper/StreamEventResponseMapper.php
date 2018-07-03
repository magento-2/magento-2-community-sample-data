<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\StreamEventInterface;
use Temando\Shipping\Model\StreamEventInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\StreamEventResponseType;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class StreamEventResponseMapper
{
    /**
     * @var StreamEventInterfaceFactory
     */
    private $streamEventFactory;

    /**
     * StreamEventResponseMapper constructor.
     *
     * @param StreamEventInterfaceFactory $streamEventFactory
     */
    public function __construct(StreamEventInterfaceFactory $streamEventFactory)
    {
        $this->streamEventFactory = $streamEventFactory;
    }

    /**
     * @param StreamEventResponseType $apiStreamEvent
     *
     * @return StreamEventInterface
     */
    public function map(StreamEventResponseType $apiStreamEvent)
    {
        $event = $this->streamEventFactory->create(['data' => [
            StreamEventInterface::EVENT_ID => $apiStreamEvent->getId(),
            StreamEventInterface::EVENT_TYPE => $apiStreamEvent->getAttributes()->getEvent(),
            StreamEventInterface::ENTITY_TYPE => $apiStreamEvent->getAttributes()->getType(),
            StreamEventInterface::ENTITY_ID => $apiStreamEvent->getAttributes()->getId(),
        ]]);

        return $event;
    }
}
