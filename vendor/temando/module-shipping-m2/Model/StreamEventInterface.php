<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model;

/**
 * Temando Event Interface.
 *
 * The event data object represents one item in the event stream for shipments to modify (CRUD)
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface StreamEventInterface
{
    const EVENT_ID = 'event_id';
    const EVENT_TYPE = 'event_type';
    const ENTITY_TYPE = 'entity_type';
    const ENTITY_ID = 'entity_id';

    const EVENT_TYPE_CREATE = 'CREATE';
    const EVENT_TYPE_MODIFY = 'MODIFY';
    const EVENT_TYPE_REMOVE = 'REMOVE';

    const ENTITY_TYPE_ORDER = 'order';
    const ENTITY_TYPE_SHIPMENT = 'shipment';

    /**
     * @return string
     */
    public function getEventId();

    /**
     * @return string
     */
    public function getEventType();

    /**
     * @return string
     */
    public function getEntityType();

    /**
     * @return string
     */
    public function getEntityId();
}
