<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipping\RateRequest;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressExtensionInterfaceFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterfaceFactory;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterfaceFactory;
use Temando\Shipping\Model\OrderInterfaceBuilder;
use Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\CollectionPointSearchRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\QuoteCollectionPointRepositoryInterface;

/**
 * Temando Rate Request Data Initializer.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class RequestDataInitializer
{
    /**
     * @var Extractor
     */
    private $rateRequestExtractor;

    /**
     * @var CollectionPointSearchRepositoryInterface
     */
    private $searchRequestRepository;

    /**
     * @var SearchRequestInterfaceFactory
     */
    private $searchRequestFactory;

    /**
     * @var QuoteCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var QuoteCollectionPointInterfaceFactory
     */
    private $collectionPointFactory;

    /**
     * @var ShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var AddressRepositoryInterface
     */
    private $checkoutAddressRepository;

    /**
     * @var AddressExtensionInterfaceFactory
     */
    private $addressExtensionFactory;

    /**
     * @var OrderInterfaceBuilder
     */
    private $orderBuilder;

    /**
     * RequestDataInitializer constructor.
     * @param Extractor $rateRequestExtractor
     * @param CollectionPointSearchRepositoryInterface $searchRequestRepository
     * @param SearchRequestInterfaceFactory $searchRequestFactory
     * @param QuoteCollectionPointRepositoryInterface $collectionPointRepository
     * @param QuoteCollectionPointInterfaceFactory $collectionPointFactory
     * @param ShippingAddressManagementInterface $addressManagement
     * @param AddressRepositoryInterface $checkoutAddressRepository
     * @param AddressExtensionInterfaceFactory $addressExtensionFactory
     * @param OrderInterfaceBuilder $orderBuilder
     */
    public function __construct(
        Extractor $rateRequestExtractor,
        CollectionPointSearchRepositoryInterface $searchRequestRepository,
        SearchRequestInterfaceFactory $searchRequestFactory,
        QuoteCollectionPointRepositoryInterface $collectionPointRepository,
        QuoteCollectionPointInterfaceFactory $collectionPointFactory,
        ShippingAddressManagementInterface $addressManagement,
        AddressRepositoryInterface $checkoutAddressRepository,
        AddressExtensionInterfaceFactory $addressExtensionFactory,
        OrderInterfaceBuilder $orderBuilder
    ) {
        $this->rateRequestExtractor = $rateRequestExtractor;
        $this->searchRequestRepository = $searchRequestRepository;
        $this->searchRequestFactory = $searchRequestFactory;
        $this->collectionPointRepository = $collectionPointRepository;
        $this->collectionPointFactory = $collectionPointFactory;
        $this->addressManagement = $addressManagement;
        $this->checkoutAddressRepository = $checkoutAddressRepository;
        $this->addressExtensionFactory = $addressExtensionFactory;
        $this->orderBuilder = $orderBuilder;
    }

    /**
     * Prepare the request type for quoting the current cart,
     * i.e. collect relevant data from rate request and persistent storage.
     *
     * @param RateRequest $rateRequest
     * @return \Temando\Shipping\Model\OrderInterface
     */
    public function getQuotingData(RateRequest $rateRequest)
    {
        $shippingAddress = $this->rateRequestExtractor->getShippingAddress($rateRequest);

        try {
            $checkoutAddress = $this->checkoutAddressRepository->getByQuoteAddressId($shippingAddress->getId());
            $checkoutFields = $checkoutAddress->getServiceSelection();
        } catch (LocalizedException $e) {
            $checkoutFields = [];
        }

        $addressExtension = $this->addressExtensionFactory->create(['data' =>
            ['checkout_fields' => $checkoutFields]
        ]);
        $shippingAddress->setExtensionAttributes($addressExtension);

        try {
            $searchRequest = $this->searchRequestRepository->get($shippingAddress->getId());
        } catch (LocalizedException $exception) {
            $searchRequest = $this->searchRequestFactory->create();
        }

        try {
            $collectionPoint = $this->collectionPointRepository->getSelected($shippingAddress->getId());
        } catch (LocalizedException $exception) {
            $collectionPoint = $this->collectionPointFactory->create();
        }

        // create remote order entity from rate request
        $this->orderBuilder->setRateRequest($rateRequest);
        $this->orderBuilder->setCollectionPointSearchRequest($searchRequest);
        $this->orderBuilder->setCollectionPoint($collectionPoint);

        /** @var \Temando\Shipping\Model\OrderInterface $order */
        $order = $this->orderBuilder->create();

        return $order;
    }

    /**
     * Prepare the request type for manifesting the current order,
     * i.e. collect relevant data from sales order and persistent storage.
     *
     * @param OrderInterface $order
     * @return \Temando\Shipping\Model\OrderInterface
     */
    public function getManifestationData(OrderInterface $order)
    {
        $searchRequest = $this->searchRequestFactory->create();

        try {
            $shippingAddress = $this->addressManagement->get($order->getQuoteId());
            $collectionPoint = $this->collectionPointRepository->getSelected($shippingAddress->getId());
        } catch (LocalizedException $exception) {
            $collectionPoint = $this->collectionPointFactory->create();
        }

        // create remote order entity from rate request
        $this->orderBuilder->setOrder($order);
        $this->orderBuilder->setCollectionPointSearchRequest($searchRequest);
        $this->orderBuilder->setCollectionPoint($collectionPoint);

        /** @var \Temando\Shipping\Model\OrderInterface $order */
        $order = $this->orderBuilder->create();

        return $order;
    }
}
