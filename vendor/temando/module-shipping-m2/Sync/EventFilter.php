<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Sync;

use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\StreamEventInterface;

/**
 * Temando Event Filter
 *
 * @package Temando\Shipping\Sync
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class EventFilter
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * EventFilter constructor.
     * @param ModuleConfigInterface $config
     */
    public function __construct(ModuleConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param StreamEventInterface[] $streamEvents
     * @return StreamEventInterface[]
     */
    public function filter(array $streamEvents)
    {
        /* Filters treated as OR condition. Empty filter leads to empty result */
        $filter = [];
        if ($this->config->isSyncOrderEnabled()) {
            $filter[] = [
                "field" => "entity_type",
                "value" => "order"
            ];
        }
        if ($this->config->isSyncShipmentEnabled()) {
            $filter[] = [
                "field" => "entity_type",
                "value" => "shipment"
            ];
        }

        // filter converted event objects
        $eventFilter = function (StreamEventInterface $streamEvent) use ($filter) {
            $match = false;

            /** @var \Temando\Shipping\Model\StreamEvent $streamEvent */
            foreach ($filter as $filterItem) {
                $value = $streamEvent->getData($filterItem['field']);
                $match = ($value === $filterItem['value']);
                if ($match) {
                    break;
                }
            }

            return $match;
        };

        $streamEvents = array_filter($streamEvents, $eventFilter);

        return $streamEvents;
    }
}
