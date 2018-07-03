<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Track Event Interface.
 *
 * A track event represents one event in the tracking status history.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface TrackEventInterface
{
    const TRACKING_EVENT_ID = 'tracking_event_id';
    const STATUS = 'status';
    const OCCURRED_AT = 'occurred_at';

    /**
     * @return string
     */
    public function getTrackingEventId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getOccurredAt();

    /**
     * @return string[]
     */
    public function getEventData();
}
