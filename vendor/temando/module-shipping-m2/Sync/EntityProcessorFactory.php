<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Sync;

use Temando\Shipping\Model\StreamEventInterface;
use Temando\Shipping\Sync\Exception\EventException;

/**
 * Temando Event Processor Factory
 *
 * @package  Temando\Shipping\Sync
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class EntityProcessorFactory
{
    /**
     * @var ShipmentProcessor
     */
    private $shipmentProcessor;

    /**
     * EntityEventProcessorFactory constructor.
     * @param ShipmentProcessor $shipmentProcessor
     */
    public function __construct(ShipmentProcessor $shipmentProcessor)
    {
        $this->shipmentProcessor = $shipmentProcessor;
    }

    /**
     * @param string $entityType
     * @return EntityProcessorInterface
     * @throws EventException
     */
    public function get($entityType)
    {
        switch ($entityType) {
            case StreamEventInterface::ENTITY_TYPE_SHIPMENT:
                return $this->shipmentProcessor;
            default:
                throw EventException::unknownEntityType($entityType);
        }
    }
}
