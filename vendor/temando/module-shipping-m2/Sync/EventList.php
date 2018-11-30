<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Sync;

/**
 * Temando Event Stack
 *
 * @package  Temando\Shipping\Sync
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class EventList extends \ArrayIterator
{
    /**
     * @param string[] $eventIds
     *
     * @return static
     */
    public static function fromArray(array $eventIds)
    {
        $events = array_combine($eventIds, $eventIds);
        return new static($events);
    }

    /**
     * @param string $eventId
     *
     * @return void
     */
    public function addEvent($eventId)
    {
        $this->offsetSet($eventId, $eventId);
    }

    /**
     * @param string $eventId
     *
     * @return void
     */
    public function removeEvent($eventId)
    {
        $this->offsetUnset($eventId);
    }

    /**
     * @param string $eventId
     *
     * @return bool
     */
    public function hasEvent($eventId)
    {
        return $this->offsetExists($eventId);
    }
}
