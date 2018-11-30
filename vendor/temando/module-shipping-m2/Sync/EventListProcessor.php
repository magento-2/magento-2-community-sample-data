<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Sync;

use Psr\Log\LoggerInterface;
use Temando\Shipping\Model\StreamEventInterface;
use Temando\Shipping\Sync\Exception\EventException;
use Temando\Shipping\Sync\Exception\EventProcessorException;

/**
 * Temando Event List Processor
 *
 * Process given stream events, usually a subset of events available at the API.
 *
 * @package  Temando\Shipping\Sync
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class EventListProcessor
{
    /**
     * @var EntityProcessorFactory
     */
    private $entityProcessorFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityProcessorFactory $entityProcessorFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityProcessorFactory $entityProcessorFactory,
        LoggerInterface $logger
    ) {
        $this->entityProcessorFactory = $entityProcessorFactory;
        $this->logger                 = $logger;
    }

    /**
     * @param StreamEventInterface[] $streamEvents
     * @param EventList $eventList
     * @return void
     */
    public function processEventList(array $streamEvents, EventList $eventList)
    {
        foreach ($streamEvents as $streamEvent) {
            if ($eventList->hasEvent($streamEvent->getEventId())) {
                // event was already processed with failure. do not try again.
                continue;
            }

            // mark event as processing
            $eventList->addEvent($streamEvent->getEventId());

            $operation = $streamEvent->getEventType();
            $entityType = $streamEvent->getEntityType();
            $entityId = $streamEvent->getEntityId();

            try {
                $entityProcessor = $this->entityProcessorFactory->get($entityType);
                $processedId = $entityProcessor->execute($operation, $entityId);

                // event was successfully processed
                $eventList->removeEvent($streamEvent->getEventId());

                $message = "Operation {$operation} was executed successfully for {$entityType} {$processedId}.";
                $this->logger->info($message);
            } catch (EventProcessorException $e) {
                // processing failed, try again later
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                continue;
            } catch (EventException $e) {
                // processing not supported or desired
                $eventList->removeEvent($streamEvent->getEventId());

                $this->logger->notice($e->getMessage(), ['exception' => $e]);
            }
        }
    }
}
