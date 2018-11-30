<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterfaceFactory;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\AbstractResource;
use Temando\Shipping\Rest\Response\DataObject\OrderQualification;
use Temando\Shipping\Rest\Response\DataObject\Shipment;
use Temando\Shipping\Rest\Response\Document\AllocateOrderInterface;
use Temando\Shipping\Rest\Response\Document\GetCollectionPointsInterface;
use Temando\Shipping\Rest\Response\Document\QualifyOrderInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterfaceFactory;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderResponseMapper
{
    /**
     * @var OrderReferenceInterfaceFactory
     */
    private $orderReferenceFactory;

    /**
     * @var ShippingExperienceInterfaceFactory
     */
    private $shippingExperienceFactory;

    /**
     * @var OrderResponseTypeInterfaceFactory
     */
    private $orderResponseFactory;

    /**
     * @var ShippingExperiencesMapper
     */
    private $shippingExperiencesMapper;

    /**
     * @var OrderAllocationResponseMapper
     */
    private $allocationMapper;

    /**
     * @var CollectionPointsResponseMapper
     */
    private $collectionPointsMapper;

    /**
     * @var PickupLocationsResponseMapper
     */
    private $pickupLocationsMapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderResponseMapper constructor.
     * @param OrderReferenceInterfaceFactory $orderReferenceFactory
     * @param ShippingExperienceInterfaceFactory $shippingExperienceFactory
     * @param OrderResponseTypeInterfaceFactory $orderResponseFactory
     * @param ShippingExperiencesMapper $shippingExperiencesMapper
     * @param OrderAllocationResponseMapper $allocationMapper
     * @param CollectionPointsResponseMapper $collectionPointsMapper
     * @param PickupLocationsResponseMapper $pickupLocationsMapper
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderReferenceInterfaceFactory $orderReferenceFactory,
        ShippingExperienceInterfaceFactory $shippingExperienceFactory,
        OrderResponseTypeInterfaceFactory $orderResponseFactory,
        ShippingExperiencesMapper $shippingExperiencesMapper,
        OrderAllocationResponseMapper $allocationMapper,
        CollectionPointsResponseMapper $collectionPointsMapper,
        PickupLocationsResponseMapper $pickupLocationsMapper,
        LoggerInterface $logger
    ) {
        $this->orderReferenceFactory = $orderReferenceFactory;
        $this->shippingExperienceFactory = $shippingExperienceFactory;
        $this->orderResponseFactory = $orderResponseFactory;
        $this->shippingExperiencesMapper =$shippingExperiencesMapper;
        $this->allocationMapper = $allocationMapper;
        $this->collectionPointsMapper = $collectionPointsMapper;
        $this->pickupLocationsMapper = $pickupLocationsMapper;
        $this->logger = $logger;
    }

    /**
     * @return OrderResponseTypeInterface
     */
    public function createEmptyResponse()
    {
        $orderResponse = $this->orderResponseFactory->create(['data' => [
            OrderResponseTypeInterface::SHIPPING_EXPERIENCES => [],
        ]]);

        return $orderResponse;
    }

    /**
     * @param AllocateOrderInterface $apiOrder
     * @return OrderResponseTypeInterface
     */
    public function mapAllocatedOrder(AllocateOrderInterface $apiOrder)
    {
        $extOrderId = $apiOrder->getData()->getId();
        $errors = $this->allocationMapper->mapErrors($apiOrder->getIncluded());
        $shipments = $this->allocationMapper->mapShipments($apiOrder->getIncluded());

        $orderResponse = $this->orderResponseFactory->create(['data' => [
            OrderResponseTypeInterface::EXT_ORDER_ID => $extOrderId,
            OrderResponseTypeInterface::ERRORS => $errors,
            OrderResponseTypeInterface::SHIPMENTS => $shipments,
        ]]);

        return $orderResponse;
    }

    /**
     * @param QualifyOrderInterface $apiOrder
     * @return OrderResponseTypeInterface
     * @throws LocalizedException
     */
    public function mapCreatedOrder(QualifyOrderInterface $apiOrder)
    {
        $extOrderId = $apiOrder->getData()->getId();

        /** @var OrderQualification[] $included */
        $apiIncluded = array_filter($apiOrder->getIncluded(), function (OrderQualification $element) {
            return ($element->getType() == 'orderQualification');
        });
        /** @var \Temando\Shipping\Rest\Response\Fields\OrderQualification\Experience[] $sets */
        $apiExperiences = array_reduce($apiIncluded, function ($experiences, OrderQualification $apiIncluded) {
            return array_merge($experiences, $apiIncluded->getAttributes()->getExperiences());
        }, []);
        $shippingExperiences = $this->shippingExperiencesMapper->map($apiExperiences);

        $orderResponse = $this->orderResponseFactory->create(['data' => [
            OrderResponseTypeInterface::EXT_ORDER_ID => $extOrderId,
            OrderResponseTypeInterface::SHIPPING_EXPERIENCES => $shippingExperiences,
        ]]);

        return $orderResponse;
    }

    /**
     * @param GetCollectionPointsInterface $apiOrder
     * @return OrderResponseTypeInterface
     * @throws LocalizedException
     */
    public function mapCollectionPoints(GetCollectionPointsInterface $apiOrder)
    {
        $extOrderId = $apiOrder->getData()->getId();
        $collectionPoints = $this->collectionPointsMapper->map($apiOrder->getIncluded());

        $orderResponse = $this->orderResponseFactory->create(['data' => [
            OrderResponseTypeInterface::EXT_ORDER_ID => $extOrderId,
            OrderResponseTypeInterface::COLLECTION_POINTS => $collectionPoints,
            OrderResponseTypeInterface::SHIPPING_EXPERIENCES => [],
        ]]);

        return $orderResponse;
    }

    /**
     * @param QualifyOrderInterface $apiOrder
     * @return OrderResponseTypeInterface
     * @throws LocalizedException
     */
    public function mapPickupLocations(QualifyOrderInterface $apiOrder)
    {
        $extOrderId = $apiOrder->getData()->getId();
        $pickupLocations = $this->pickupLocationsMapper->map($apiOrder->getIncluded());

        $orderResponse = $this->orderResponseFactory->create(['data' => [
            OrderResponseTypeInterface::EXT_ORDER_ID => $extOrderId,
            OrderResponseTypeInterface::PICKUP_LOCATIONS => $pickupLocations,
            OrderResponseTypeInterface::SHIPPING_EXPERIENCES => [],
        ]]);

        return $orderResponse;
    }

    /**
     * @param QualifyOrderInterface $apiOrder
     * @return OrderResponseTypeInterface
     */
    public function mapUpdatedOrder(QualifyOrderInterface $apiOrder)
    {
        $extOrderId = $apiOrder->getData()->getId();

        $orderResponse = $this->orderResponseFactory->create(['data' => [
            OrderResponseTypeInterface::EXT_ORDER_ID => $extOrderId,
        ]]);

        return $orderResponse;
    }
}
