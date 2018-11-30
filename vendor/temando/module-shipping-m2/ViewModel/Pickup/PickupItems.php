<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Pickup;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\Model\PickupProviderInterface;

/**
 * View model for Pickup Item blocks.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Sebastian Ertner<sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupItems implements ArgumentInterface
{
    /**
     * @var PickupProviderInterface
     */
    private $pickupProvider;

    /**
     * PickupItems constructor.
     * @param PickupProviderInterface $pickupProvider
     */
    public function __construct(PickupProviderInterface $pickupProvider)
    {
        $this->pickupProvider = $pickupProvider;
    }

    /**
     * @return OrderItemInterface[]
     */
    public function getAllOrderItems(): array
    {
        $items = [];

        /** @var Order $order */
        $order = $this->pickupProvider->getOrder();
        $orderItems = $order->getAllItems();
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getParentItem()) {
                continue;
            }
            $items[] = $orderItem;
        }

        return $items;
    }

    /**
     * Depending on the pickup state, obtain an appropriate "Quantity" label/heading.
     *
     * @return string
     */
    public function getQtyLabel()
    {
        $pickup = $this->pickupProvider->getPickup();
        switch ($pickup->getState()) {
            case PickupInterface::STATE_REQUESTED:
                return 'Qty Prepared';
            case PickupInterface::STATE_READY:
                return 'Qty Packed';
            case PickupInterface::STATE_PICKED_UP:
                return 'Qty Collected';
            default:
                return 'Quantity';
        }
    }

    /**
     * @param string $sku
     * @return int
     */
    public function getQtyPrepared(string $sku): int
    {
        $qty = 0;

        foreach ($this->pickupProvider->getPickups() as $pickup) {
            if ($pickup->getState() !== PickupInterface::STATE_READY) {
                continue;
            }

            $pickupItems = $pickup->getItems();
            if (!isset($pickupItems[$sku])) {
                continue;
            }

            $qty += $pickupItems[$sku];
        }

        return $qty;
    }

    /**
     * Obtain the item quantity included with the current pickup.
     *
     * @param string $sku
     * @return int
     */
    public function getItemQty(string $sku): int
    {
        $pickup = $this->pickupProvider->getPickup();
        $items = $pickup->getItems();
        if (!isset($items[$sku])) {
            return 0;
        }

        return $items[$sku];
    }
}
