<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\ViewModel\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Temando\Shipping\Api\Data\Delivery\OrderCollectionPointInterface;
use Temando\Shipping\Api\Data\Delivery\OrderPickupLocationInterface;
use Temando\Shipping\Model\Location\OrderAddressFactory;
use Temando\Shipping\Model\ResourceModel\Repository\OrderCollectionPointRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderPickupLocationRepositoryInterface;
use Temando\Shipping\ViewModel\DataProvider\OrderAddress as AddressRenderer;

/**
 * View model for order locations.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Location implements ArgumentInterface
{
    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var OrderCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var OrderPickupLocationRepositoryInterface
     */
    private $pickupLocationRepository;

    /**
     * @var OrderAddressFactory
     */
    private $orderAddressFactory;

    /**
     * @var OrderCollectionPointInterface
     */
    private $collectionPoint;

    /**
     * @var OrderPickupLocationInterface
     */
    private $pickupLocation;

    /**
     * Location constructor.
     *
     * @param AddressRenderer $addressRenderer
     * @param OrderCollectionPointRepositoryInterface $collectionPointRepository
     * @param OrderPickupLocationRepositoryInterface $pickupLocationRepository
     * @param OrderAddressFactory $orderAddressFactory
     */
    public function __construct(
        AddressRenderer $addressRenderer,
        OrderCollectionPointRepositoryInterface $collectionPointRepository,
        OrderPickupLocationRepositoryInterface $pickupLocationRepository,
        OrderAddressFactory $orderAddressFactory
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->collectionPointRepository = $collectionPointRepository;
        $this->pickupLocationRepository = $pickupLocationRepository;
        $this->orderAddressFactory = $orderAddressFactory;
    }

    /**
     * @param OrderInterface|Order $order
     *
     * @return OrderCollectionPointInterface|null
     */
    private function getCollectionPoint(OrderInterface $order)
    {
        if (!$this->collectionPoint) {
            try {
                $shippingAddressId = $order->getData('shipping_address_id');
                $this->collectionPoint = $this->collectionPointRepository->get($shippingAddressId);
            } catch (LocalizedException $e) {
                $this->collectionPoint = null;
            }
        }

        return $this->collectionPoint;
    }

    /**
     * @param OrderInterface|Order $order
     *
     * @return OrderPickupLocationInterface|null
     */
    private function getPickupLocation(OrderInterface $order)
    {
        if (!$this->pickupLocation) {
            try {
                $shippingAddressId = $order->getData('shipping_address_id');
                $this->pickupLocation = $this->pickupLocationRepository->get($shippingAddressId);
            } catch (LocalizedException $e) {
                $this->pickupLocation = null;
            }
        }

        return $this->pickupLocation;
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function hasDeliveryLocation(OrderInterface $order)
    {
        $collectionPoint = $this->getCollectionPoint($order);
        $pickUpLocation = $this->getPickupLocation($order);

        return ($collectionPoint || $pickUpLocation);
    }

    /**
     * @param OrderInterface $order
     *
     * @return string
     */
    public function getDeliveryLocationTitle(OrderInterface $order)
    {
        if ($this->getCollectionPoint($order)) {
            return __('Collection Point');
        } elseif ($this->getPickupLocation($order)) {
            return __('Pickup Location');
        } else {
            return '';
        }
    }

    /**
     * @param OrderInterface|Order $order
     * @return string
     */
    public function getBillingAddressHtml(OrderInterface $order)
    {
        return $this->addressRenderer->getFormattedAddress($order->getBillingAddress());
    }

    /**
     * @param OrderInterface|Order $order
     * @return string
     */
    public function getShippingAddressHtml(OrderInterface $order)
    {
        return $this->addressRenderer->getFormattedAddress($order->getShippingAddress());
    }

    /**
     * @param OrderInterface|Order $order
     *
     * @return string
     */
    public function getDeliveryLocationAddressHtml(OrderInterface $order)
    {
        $collectionPoint = $this->getCollectionPoint($order);
        if ($collectionPoint !== null) {
            $address = $this->orderAddressFactory->createFromCollectionPoint($collectionPoint);
            return $this->addressRenderer->getFormattedAddress($address);
        }

        $pickupLocation = $this->getPickupLocation($order);
        if ($pickupLocation !== null) {
            $address = $this->orderAddressFactory->createFromPickupLocation($pickupLocation);
            return $this->addressRenderer->getFormattedAddress($address);
        }

        return '';
    }
}
