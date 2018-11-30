<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Pickup;

use Magento\Sales\Api\Data\OrderItemInterface;
use Temando\Shipping\Model\PickupInterface;

/**
 * Temando Pickup Management
 *
 * @package Temando\Shipping\Model
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupManagement
{
    /**
     * @var PickupInterface[]
     */
    private $pickups = [];

    /**
     * PickupManagement constructor.
     * @param PickupInterface[] $pickups
     */
    public function __construct(
        array $pickups = []
    ) {
        $this->pickups = $pickups;
    }

    /**
     * @param string $state
     * @return PickupInterface[]
     */
    public function getPickupsByState(string $state): array
    {
        $pickups = array_filter($this->pickups, function (PickupInterface $pickup) use ($state) {
            return $pickup->getState() === $state;
        });

        return $pickups;
    }

    /**
     * Check if a pickup fulfillment with given ID can be cancelled.
     *
     * @param string $pickupId
     * @return bool
     */
    public function canCancel(string $pickupId): bool
    {
        if (!isset($this->pickups[$pickupId])) {
            return false;
        }

        $pickup = $this->pickups[$pickupId];
        $canCancel = !in_array(
            $pickup->getState(),
            [PickupInterface::STATE_CANCELLED, PickupInterface::STATE_PICKED_UP]
        );

        return $canCancel;
    }

    /**
     * Check if a pickup fulfillment with given ID can be prepared for collection.
     *
     * @param string $pickupId
     * @return bool
     */
    public function canPrepare(string $pickupId): bool
    {
        if (!isset($this->pickups[$pickupId])) {
            return false;
        }

        $pickup = $this->pickups[$pickupId];
        $canPrepare = ($pickup->getState() == PickupInterface::STATE_REQUESTED);

        return $canPrepare;
    }

    /**
     * Check if a pickup fulfillment with given ID can be collected from location.
     *
     * @param string $pickupId
     * @return bool
     */
    public function canCollect(string $pickupId): bool
    {
        if (!isset($this->pickups[$pickupId])) {
            return false;
        }

        $pickup = $this->pickups[$pickupId];
        $canCollect = ($pickup->getState() == PickupInterface::STATE_READY);

        return $canCollect;
    }

    /**
     * Obtain a list of items that are ready for collection.
     * Return format: [<sku> => <qty>, <sku> => <qty>]
     *
     * @return mixed[]
     */
    public function getPreparedItems()
    {
        if (empty($this->pickups)) {
            return [];
        }

        $preparedPickups = $this->getPickupsByState(PickupInterface::STATE_READY);
        $preparedItems = array_reduce($preparedPickups, function ($carry, PickupInterface $pickup) {
            foreach ($pickup->getItems() as $sku => $quantity) {
                if (isset($carry[$sku])) {
                    $carry[$sku]+= $quantity;
                } else {
                    $carry[$sku] = $quantity;
                }
            }

            return $carry;
        }, []);

        return $preparedItems;
    }

    /**
     * Obtain a list of items that are not yet shipped, prepared, or collected.
     * Return format: [<sku> => <qty>, <sku> => <qty>]
     *
     * @param OrderItemInterface[] $orderItems
     * @return int[]
     */
    public function getOpenItems(array $orderItems)
    {
        $openItems = [];
        $preparedItems = $this->getPreparedItems();

        foreach ($orderItems as $orderItem) {
            if ($orderItem->getIsVirtual() || $orderItem->getParentItem()) {
                continue;
            }

            $sku = $orderItem->getSku();
            $qtyOrdered = $orderItem->getQtyOrdered();
            $qtyShipped = $orderItem->getQtyShipped();
            $qtyPrepared = isset($preparedItems[$sku]) ? $preparedItems[$sku] : 0;

            $openItems[$sku] = $qtyOrdered - $qtyShipped - $qtyPrepared;
        }

        $openItems = array_filter($openItems, function ($qty) {
            return $qty > 0;
        });

        return $openItems;
    }

    /**
     * Check if requested items can be fulfilled. Returns a subset of the requested
     * items in case some of them are already fulfilled.
     * Return format: [<sku> => <qty>, <sku> => <qty>]
     *
     * @param mixed[] $requestedItems Format: [<order_item_id> => <qty>, <order_item_id> => <qty>]
     * @param OrderItemInterface[] $orderItems
     * @return int[]
     */
    public function getRequestedItems(array $requestedItems, array $orderItems)
    {
        $openItems = $this->getOpenItems($orderItems);
        $acceptedItems = [];

        foreach ($orderItems as $orderItem) {
            if ($orderItem->getParentItem()) {
                continue;
            }

            $sku = $orderItem->getSku();
            $id = $orderItem->getId();

            $qtyRequested = isset($requestedItems[$id]) ? $requestedItems[$id] : 0;
            $qtyOpen = isset($openItems[$sku]) ? $openItems[$sku] : 0;

            $qtyRequested = min($qtyRequested, $qtyOpen);

            if ($qtyRequested < 1) {
                continue;
            }

            $acceptedItems[$sku] = $qtyRequested;
        }

        return $acceptedItems;
    }
}
