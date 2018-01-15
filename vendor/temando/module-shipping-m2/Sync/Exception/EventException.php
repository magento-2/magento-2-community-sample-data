<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Sync\Exception;

/**
 * Temando General Event Exception
 *
 * The event cannot and should not get handled.
 *
 * @package  Temando\Shipping\Sync
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class EventException extends \Exception
{
    /**
     * @param string $entityType
     * @param \Exception|null $previous
     * @return static
     */
    public static function unknownEntityType($entityType, \Exception $previous = null)
    {
        return new static("Entity type '$entityType' cannot be processed.", 0, $previous);
    }

    /**
     * @param string $entityType
     * @param string $eventType
     * @return static
     */
    public static function unknownOperation($entityType, $eventType)
    {
        return new static("The '$eventType' operation is not supported for '$entityType' entities.");
    }

    /**
     * @param string $entityType
     * @param string $eventType
     * @param string $entityId
     * @param string $reason
     * @return static
     */
    public static function operationSkipped($entityType, $eventType, $entityId, $reason = '')
    {
        $message = "The '$eventType' operation was skipped for {$entityType} {$entityId}";
        if ($reason) {
            $message.= ": $reason";
        } else {
            $message.= '.';
        }

        return new static($message);
    }
}
