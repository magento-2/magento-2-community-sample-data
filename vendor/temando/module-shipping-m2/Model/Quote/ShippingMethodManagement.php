<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Quote\Api\Data\AddressExtensionInterface;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Quote\Api\Data\AddressExtensionInterfaceFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Temando\Shipping\Api\Quote\ShippingMethodManagementInterface;

class ShippingMethodManagement implements ShippingMethodManagementInterface
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Customer Address repository
     *
     * @var AddressRepositoryInterface
     */
    private $customerAddressRepository;

    /**
     * @var AddressInterfaceFactory
     */
    private $quoteAddressFactory;

    /**
     * @var AddressExtensionInterfaceFactory
     */
    private $quoteAddressExtensionFactory;

    /**
     * Original shipping method management.
     *
     * @var ShipmentEstimationInterface
     */
    private $shipmentEstimator;

    /**
     * ShippingMethodManagement
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory $quoteAddressFactory
     * @param AddressExtensionInterfaceFactory $quoteAddressExtensionFactory
     * @param ShipmentEstimationInterface $shipmentEstimator
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $quoteAddressFactory,
        AddressExtensionInterfaceFactory $quoteAddressExtensionFactory,
        ShipmentEstimationInterface $shipmentEstimator
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->customerAddressRepository = $addressRepository;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->quoteAddressExtensionFactory = $quoteAddressExtensionFactory;
        $this->shipmentEstimator = $shipmentEstimator;
    }

    /**
     * Estimate shipping with extension attributes
     *
     * @see \Magento\Quote\Api\ShippingMethodManagementInterface::estimateByAddressId
     *
     * @param int $cartId The shopping cart ID.
     * @param int $addressId The estimate address id
     * @param \Magento\Quote\Api\Data\AddressExtensionInterface|null $extensionAttributes
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     */
    public function estimateByAddressId($cartId, $addressId, AddressExtensionInterface $extensionAttributes = null)
    {
        $customerAddress = $this->customerAddressRepository->getById($addressId);

        /** @var \Magento\Quote\Model\Quote\Address $quoteAddress */
        $quoteAddress = $this->quoteAddressFactory->create();
        $quoteAddress->importCustomerAddressData($customerAddress);

        if (empty($extensionAttributes)) {
            return $this->shipmentEstimator->estimateByExtendedAddress($cartId, $quoteAddress);
        }

        $estimationExtensionAttributes = $quoteAddress->getExtensionAttributes();
        if (empty($estimationExtensionAttributes)) {
            $estimationExtensionAttributes = $this->quoteAddressExtensionFactory->create();
        }

        $estimationExtensionAttributes->setCheckoutFields($extensionAttributes->getCheckoutFields());
        $quoteAddress->setExtensionAttributes($estimationExtensionAttributes);

        return $this->shipmentEstimator->estimateByExtendedAddress($cartId, $quoteAddress);
    }
}
