<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\EventStream;

use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Temando Shipment Event Stream Repository Interface.
 *
 * For cases where the Temando shipment was externally created,
 * we have to ask for shipments which have to synced with Magento.
 *
 * After processing these events, it has to be deleted.
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface EventRepositoryInterface
{
    /**
     * @param string $streamId
     * @param int|null $offset
     * @param int|null $limit
     *
     * @return \Temando\Shipping\Model\StreamEventInterface[]
     */
    public function getEventList($streamId, $offset = null, $limit = null);

    /**
     * @param string $streamId
     * @param string $eventId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete($streamId, $eventId);
}
