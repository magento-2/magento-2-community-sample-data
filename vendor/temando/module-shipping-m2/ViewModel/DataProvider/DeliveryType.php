<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\Quote\DeliveryOptionManagement;
use Temando\Shipping\Model\ResourceModel\Repository\OrderCollectionPointRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderPickupLocationRepositoryInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Order Delivery Type
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class DeliveryType
{
    /**
     * @var OrderPickupLocationRepositoryInterface
     */
    private $pickupLocationRepository;

    /**
     * @var OrderCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var string[]
     */
    private $orderDeliveryType;

    /**
     * DeliveryType constructor.
     * @param OrderPickupLocationRepositoryInterface $pickupLocationRepository
     * @param OrderCollectionPointRepositoryInterface $collectionPointRepository
     * @param \string[] $orderDeliveryType
     */
    public function __construct(
        OrderPickupLocationRepositoryInterface $pickupLocationRepository,
        OrderCollectionPointRepositoryInterface $collectionPointRepository,
        array $orderDeliveryType = []
    ) {
        $this->pickupLocationRepository = $pickupLocationRepository;
        $this->collectionPointRepository = $collectionPointRepository;
        $this->orderDeliveryType = $orderDeliveryType;
    }

    /**
     * @param OrderAddressInterface|\Magento\Sales\Model\Order\Address $shippingAddress
     * @return bool
     */
    private function hasPickupLocation(OrderAddressInterface $shippingAddress)
    {
        try {
            $pickupLocation = $this->pickupLocationRepository->get($shippingAddress->getId());
            return (bool)$pickupLocation->getPickupLocationId();
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * @param OrderAddressInterface|\Magento\Sales\Model\Order\Address $shippingAddress
     * @return bool
     */
    private function hasCollectionPoint(OrderAddressInterface $shippingAddress)
    {
        try {
            $collectionPoint = $this->collectionPointRepository->get($shippingAddress->getId());
            return (bool)$collectionPoint->getCollectionPointId();
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * @param OrderInterface|\Magento\Sales\Model\Order $order
     * @return bool|string
     */
    private function getDeliveryType(OrderInterface $order)
    {
        $orderId = $order->getEntityId();

        if (isset($this->orderDeliveryType[$orderId])) {
            return $this->orderDeliveryType[$orderId];
        }

        if ($order->getIsVirtual() || !$order->getShippingAddress() || !$order->getData('shipping_method')) {
            // no shipping at all
            $this->orderDeliveryType[$orderId] = DeliveryOptionManagement::DELIVERY_OPTION_NONE;
            return $this->orderDeliveryType[$orderId];
        }

        $shippingMethod = $order->getShippingMethod(true);
        $carrierCode = $shippingMethod->getData('carrier_code');
        if (!$carrierCode === Carrier::CODE) {
            // no Temando shipping
            $this->orderDeliveryType[$orderId] = DeliveryOptionManagement::DELIVERY_OPTION_NONE;
            return $this->orderDeliveryType[$orderId];
        }

        /** @var \Magento\Sales\Model\Order $order */
        $shippingAddress = $order->getShippingAddress();
        if ($this->hasPickupLocation($shippingAddress)) {
            $this->orderDeliveryType[$orderId] = DeliveryOptionManagement::DELIVERY_OPTION_PICKUP;
            return $this->orderDeliveryType[$orderId];
        }

        if ($this->hasCollectionPoint($shippingAddress)) {
            $this->orderDeliveryType[$orderId] = DeliveryOptionManagement::DELIVERY_OPTION_COLLECTION_POINT;
            return $this->orderDeliveryType[$orderId];
        }

        $this->orderDeliveryType[$orderId] = DeliveryOptionManagement::DELIVERY_OPTION_ADDRESS;
        return $this->orderDeliveryType[$orderId];
    }

    /**
     * @param OrderInterface $order
     * @return bool
     */
    public function isPickupOrder(OrderInterface $order)
    {
        $deliveryType = $this->getDeliveryType($order);
        return ($deliveryType === DeliveryOptionManagement::DELIVERY_OPTION_PICKUP);
    }

    /**
     * @param OrderInterface $order
     * @return bool
     */
    public function isCollectionPointOrder(OrderInterface $order)
    {
        $deliveryType = $this->getDeliveryType($order);
        return ($deliveryType === DeliveryOptionManagement::DELIVERY_OPTION_COLLECTION_POINT);
    }
}
