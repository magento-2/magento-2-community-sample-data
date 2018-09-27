<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\Shipment\TrackEventInterface;
use Temando\Shipping\Model\Shipment\TrackEventInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\TrackingEventResponseType;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class TrackingResponseMapper
{
    /**
     * @var TrackEventInterfaceFactory
     */
    private $trackEventFactory;

    /**
     * TrackingResponseMapper constructor.
     * @param TrackEventInterfaceFactory $trackEventFactory
     */
    public function __construct(TrackEventInterfaceFactory $trackEventFactory)
    {
        $this->trackEventFactory = $trackEventFactory;
    }

    /**
     * @param TrackingEventResponseType $apiTrackingEvent
     * @return TrackEventInterface
     */
    public function map(TrackingEventResponseType $apiTrackingEvent)
    {
        $trackEvent = $this->trackEventFactory->create(['data' => [
            TrackEventInterface::TRACKING_EVENT_ID => $apiTrackingEvent->getId(),
            TrackEventInterface::STATUS => $apiTrackingEvent->getAttributes()->getStatus(),
            TrackEventInterface::OCCURRED_AT => $apiTrackingEvent->getAttributes()->getOccurredAt(),
        ]]);

        return $trackEvent;
    }
}
