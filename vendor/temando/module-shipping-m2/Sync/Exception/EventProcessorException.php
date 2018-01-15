<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Sync\Exception;

/**
 * Temando Event Processing Exception.
 *
 * The event should get handled but an error occurred.
 *
 * @package  Temando\Shipping\Sync
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class EventProcessorException extends \Exception
{
    /**
     * @param string $entityType
     * @param string $entityId
     * @param \Exception|null $previous
     * @return static
     */
    public static function processingFailed($entityType, $entityId, \Exception $previous = null)
    {
        return new static("Entity '$entityId' of type '$entityType' could not be processed.", 0, $previous);
    }
}
