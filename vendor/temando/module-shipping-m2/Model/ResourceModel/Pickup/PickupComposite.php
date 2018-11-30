<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Pickup;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Temando\Shipping\Model\PickupInterface;

/**
 * Temando Pickup Order Data Aggregator
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupComposite
{
    /**
     * @var OrderSearchResultInterface
     */
    private $orderSearchResult;

    /**
     * PickupComposite constructor.
     * @param OrderSearchResultInterface $orderSearchResult
     */
    public function __construct(OrderSearchResultInterface $orderSearchResult)
    {
        $this->orderSearchResult = $orderSearchResult;
    }

    /**
     * @param int $orderId
     * @return OrderInterface|null
     */
    private function getOrder($orderId)
    {
        foreach ($this->orderSearchResult->getItems() as $order) {
            if ($order->getEntityId() === $orderId) {
                return $order;
            }
        }

        return null;
    }

    /**
     * Add order details to pickup fulfillments
     *
     * @param PickupInterface|\Temando\Shipping\Model\Pickup $pickup
     * @param int $orderId
     * @return PickupInterface
     */
    public function aggregate(PickupInterface $pickup, $orderId)
    {
        $order = $this->getOrder($orderId);
        if (!$order) {
            return $pickup;
        }

        /** @var \Magento\Sales\Model\Order\Address $shippingAddress */
        $shippingAddress = $order->getShippingAddress();
        $customerName = [
            $shippingAddress->getFirstname(),
            $shippingAddress->getMiddlename(),
            $shippingAddress->getLastname(),
        ];
        $customerName = array_filter($customerName);

        $pickup->addData([
            'sales_order_id' => $order->getEntityId(),
            'sales_order_increment_id' => $order->getIncrementId(),
            'ordered_at_date' => $order->getCreatedAt(),
            'customer_name' => implode(' ', $customerName),
            'pickup_location' => $pickup->getPickupLocation()->getName(),
        ]);

        return $pickup;
    }
}
