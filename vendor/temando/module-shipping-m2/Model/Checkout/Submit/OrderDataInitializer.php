<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Checkout\Submit;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\OrderInterfaceBuilder;
use Temando\Shipping\Model\ResourceModel\Repository\QuoteCollectionPointRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\QuotePickupLocationRepositoryInterface;

/**
 * Temando Order Data Initializer.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderDataInitializer
{
    /**
     * @var QuoteCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var QuotePickupLocationRepositoryInterface
     */
    private $pickupLocationRepository;

    /**
     * @var ShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var OrderInterfaceBuilder
     */
    private $orderBuilder;

    /**
     * OrderDataInitializer constructor.
     * @param QuoteCollectionPointRepositoryInterface $collectionPointRepository
     * @param QuotePickupLocationRepositoryInterface $pickupLocationRepository
     * @param ShippingAddressManagementInterface $addressManagement
     * @param OrderInterfaceBuilder $orderBuilder
     */
    public function __construct(
        QuoteCollectionPointRepositoryInterface $collectionPointRepository,
        QuotePickupLocationRepositoryInterface $pickupLocationRepository,
        ShippingAddressManagementInterface $addressManagement,
        OrderInterfaceBuilder $orderBuilder
    ) {
        $this->collectionPointRepository = $collectionPointRepository;
        $this->pickupLocationRepository = $pickupLocationRepository;
        $this->addressManagement = $addressManagement;
        $this->orderBuilder = $orderBuilder;
    }

    /**
     * If consumer selected a delivery location during checkout, it will be
     * added to the order builder here.
     *
     * @param AddressInterface $address
     * @return bool
     */
    private function setDeliveryLocation(AddressInterface $address = null)
    {
        if ($address === null) {
            return false;
        }

        try {
            $collectionPoint = $this->collectionPointRepository->getSelected($address->getId());
            $this->orderBuilder->setCollectionPoint($collectionPoint);
        } catch (LocalizedException $exception) {
            $collectionPoint = null;
        }

        if ($collectionPoint) {
            return true;
        }

        try {
            $pickupLocation = $this->pickupLocationRepository->getSelected($address->getId());
            $this->orderBuilder->setPickupLocation($pickupLocation);
        } catch (LocalizedException $exception) {
            $pickupLocation = null;
        }

        return (bool)$pickupLocation;
    }

    /**
     * Create a Temando order for manifestation purposes when checkout is completed.
     *
     * The order is being built from the placed order.
     * The order may include
     * - dynamic checkout fields,
     * - delivery location selected during checkout.
     *
     * NOTE: the delivery locations will only be associated to the order address
     * after the order was successfully created at the platform. During the process,
     * read the selected location from the quote address.
     *
     * @param OrderInterface $order
     * @return \Temando\Shipping\Model\OrderInterface
     */
    public function getOrder(OrderInterface $order)
    {
        try {
            $shippingAddress = $this->addressManagement->get($order->getQuoteId());
        } catch (NoSuchEntityException $exception) {
            $shippingAddress = null;
        }

        $this->setDeliveryLocation($shippingAddress);
        $this->orderBuilder->setOrder($order);

        /** @var \Temando\Shipping\Model\OrderInterface $order */
        $order = $this->orderBuilder->create();

        return $order;
    }
}
