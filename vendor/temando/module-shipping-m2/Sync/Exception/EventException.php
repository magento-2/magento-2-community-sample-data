<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Sync\Exception;

use Magento\Framework\Exception\LocalizedException;

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
class EventException extends LocalizedException
{
    /**
     * @param string          $entityType
     * @param \Exception|null $previous
     *
     * @return static
     */
    public static function unknownEntityType($entityType, \Exception $previous = null)
    {
        $phrase = __("Entity type '%1' cannot be processed.", $entityType);

        return new static($phrase, $previous);
    }

    /**
     * @param string $entityType
     * @param string $eventType
     *
     * @return static
     */
    public static function unknownOperation($entityType, $eventType)
    {
        $phrase = __("The '%1' operation is not supported for '%2' entities.", $eventType, $entityType);

        return new static($phrase);
    }

    /**
     * @param string $entityType
     * @param string $eventType
     * @param string $entityId
     * @param string $reason
     *
     * @return static
     */
    public static function operationSkipped($entityType, $eventType, $entityId, $reason = '')
    {
        $phrase = __(
            "The '%1' operation was skipped for %2 %3%4",
            $eventType,
            $entityType,
            $entityId,
            $reason ? ": $reason" : '.'
        );

        return new static($phrase);
    }
}
