<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Sync\Exception;

use Magento\Framework\Exception\LocalizedException;

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
class EventProcessorException extends LocalizedException
{
    /**
     * @param string $entityType
     * @param string $entityId
     * @param \Exception|null $previous
     * @return static
     */
    public static function processingFailed($entityType, $entityId, \Exception $previous = null)
    {
        $phrase = __("Entity '%1' of type '%2' could not be processed.", $entityId, $entityType);

        return new static($phrase, $previous);
    }
}
