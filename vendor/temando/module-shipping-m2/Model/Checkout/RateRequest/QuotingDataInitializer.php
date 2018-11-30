<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Checkout\RateRequest;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressExtensionInterface;
use Magento\Quote\Api\Data\AddressExtensionInterfaceFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Model\OrderInterfaceBuilder;
use Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\CollectionPointSearchRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\PickupLocationSearchRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\QuoteCollectionPointRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\QuotePickupLocationRepositoryInterface;

/**
 * Temando Quoting Data Initializer.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class QuotingDataInitializer
{
    /**
     * @var Extractor
     */
    private $rateRequestExtractor;

    /**
     * @var AddressRepositoryInterface
     */
    private $checkoutAddressRepository;

    /**
     * @var AddressExtensionInterfaceFactory
     */
    private $addressExtensionFactory;

    /**
     * @var CollectionPointSearchRepositoryInterface
     */
    private $collectionPointSearchRepository;

    /**
     * @var QuoteCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var PickupLocationSearchRepositoryInterface
     */
    private $pickupLocationSearchRepository;

    /**
     * @var QuotePickupLocationRepositoryInterface
     */
    private $pickupLocationRepository;

    /**
     * @var OrderInterfaceBuilder
     */
    private $orderBuilder;

    /**
     * QuotingDataInitializer constructor.
     * @param Extractor $rateRequestExtractor
     * @param AddressRepositoryInterface $checkoutAddressRepository
     * @param AddressExtensionInterfaceFactory $addressExtensionFactory
     * @param CollectionPointSearchRepositoryInterface $collectionPointSearchRepository
     * @param QuoteCollectionPointRepositoryInterface $collectionPointRepository
     * @param PickupLocationSearchRepositoryInterface $pickupLocationSearchRepository
     * @param QuotePickupLocationRepositoryInterface $pickupLocationRepository
     * @param OrderInterfaceBuilder $orderBuilder
     */
    public function __construct(
        Extractor $rateRequestExtractor,
        AddressRepositoryInterface $checkoutAddressRepository,
        AddressExtensionInterfaceFactory $addressExtensionFactory,
        CollectionPointSearchRepositoryInterface $collectionPointSearchRepository,
        QuoteCollectionPointRepositoryInterface $collectionPointRepository,
        PickupLocationSearchRepositoryInterface $pickupLocationSearchRepository,
        QuotePickupLocationRepositoryInterface $pickupLocationRepository,
        OrderInterfaceBuilder $orderBuilder
    ) {
        $this->rateRequestExtractor = $rateRequestExtractor;
        $this->checkoutAddressRepository = $checkoutAddressRepository;
        $this->addressExtensionFactory = $addressExtensionFactory;
        $this->collectionPointSearchRepository = $collectionPointSearchRepository;
        $this->collectionPointRepository = $collectionPointRepository;
        $this->pickupLocationSearchRepository = $pickupLocationSearchRepository;
        $this->pickupLocationRepository = $pickupLocationRepository;
        $this->orderBuilder = $orderBuilder;
    }

    /**
     * If consumer selected any value-added services during checkout, these
     * are added to the shipping address here. Shipping address is part of the
     * rate request, which gets added to the order builder separately.
     *
     * @param AddressInterface $address
     * @return bool
     */
    private function setCheckoutFields(AddressInterface $address)
    {
        try {
            $checkoutAddress = $this->checkoutAddressRepository->getByQuoteAddressId($address->getId());
            $checkoutFields = $checkoutAddress->getServiceSelection();
        } catch (LocalizedException $e) {
            $checkoutFields = [];
        }

        $addressExtension = $address->getExtensionAttributes();
        if ($addressExtension instanceof AddressExtensionInterface) {
            $addressExtension->setCheckoutFields($checkoutFields);
        } else {
            $addressExtension = $this->addressExtensionFactory->create(['data' => [
                'checkout_fields' => $checkoutFields,
            ]]);
        }
        $address->setExtensionAttributes($addressExtension);

        return !empty($checkoutFields);
    }

    /**
     * If consumer selected a collection point or triggered a search for
     * collection points during checkout, these entities will be added to the
     * order builder here.
     *
     * @param AddressInterface $address
     * @return bool
     */
    private function setCollectionPoint(AddressInterface $address)
    {
        try {
            $collectionPointSearchRequest = $this->collectionPointSearchRepository->get($address->getId());
            $this->orderBuilder->setCollectionPointSearchRequest($collectionPointSearchRequest);
        } catch (LocalizedException $exception) {
            $collectionPointSearchRequest = null;
        }

        try {
            $collectionPoint = $this->collectionPointRepository->getSelected($address->getId());
            $this->orderBuilder->setCollectionPoint($collectionPoint);
        } catch (LocalizedException $exception) {
            $collectionPoint = null;
        }

        return ($collectionPointSearchRequest || $collectionPoint);
    }

    /**
     * If consumer selected a pickup location or triggered a search for
     * pickup locations during checkout, these entities will be added to the
     * order builder here.
     *
     * @param AddressInterface $address
     * @return bool
     */
    private function setPickupLocation(AddressInterface $address)
    {
        try {
            $pickupLocationSearchRequest = $this->pickupLocationSearchRepository->get($address->getId());
            $this->orderBuilder->setPickupLocationSearchRequest($pickupLocationSearchRequest);
        } catch (LocalizedException $exception) {
            $pickupLocationSearchRequest = null;
        }

        try {
            $pickupLocation = $this->pickupLocationRepository->getSelected($address->getId());
            $this->orderBuilder->setPickupLocation($pickupLocation);
        } catch (LocalizedException $exception) {
            $pickupLocation = null;
        }

        return ($pickupLocationSearchRequest || $pickupLocation);
    }

    /**
     * Create a Temando order for quoting purposes.
     *
     * The order is being built from the quote and rate request.
     * The order may include
     * - dynamic checkout fields,
     * - delivery location selected during checkout.
     *
     * @param RateRequest $rateRequest
     * @return \Temando\Shipping\Model\OrderInterface
     */
    public function getOrder(RateRequest $rateRequest)
    {
        $shippingAddress = $this->rateRequestExtractor->getShippingAddress($rateRequest);

        $this->setCheckoutFields($shippingAddress);
        $this->setCollectionPoint($shippingAddress) || $this->setPickupLocation($shippingAddress);
        $this->orderBuilder->setRateRequest($rateRequest);

        /** @var \Temando\Shipping\Model\OrderInterface $order */
        $order = $this->orderBuilder->create();

        return $order;
    }
}
