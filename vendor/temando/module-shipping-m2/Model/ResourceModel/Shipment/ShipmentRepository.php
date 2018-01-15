<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Shipment;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track as TrackResource;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterfaceFactory;
use Temando\Shipping\Model\Shipment\TrackEventInterface;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Shipment\ShipmentReference as ShipmentReferenceResource;
use Temando\Shipping\Rest\Adapter\ShipmentApiInterface;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\EntityMapper\TrackingResponseMapper;
use Temando\Shipping\Rest\EntityMapper\ShipmentResponseMapper;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\TrackingEventResponseType;

/**
 * Temando Shipment Repository
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentRepository implements ShipmentRepositoryInterface
{
    /**
     * @var ShipmentApiInterface
     */
    private $apiAdapter;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $requestFactory;

    /**
     * @var ShipmentResponseMapper
     */
    private $shipmentMapper;

    /**
     * @var TrackingResponseMapper
     */
    private $trackMapper;

    /**
     * @var ShipmentReferenceResource
     */
    private $resource;

    /**
     * @var ShipmentReferenceInterfaceFactory
     */
    private $shipmentReferenceFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var  MetadataPool
     */
    private $metadataPool;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var ShipmentTrackRepositoryInterface
     */
    private $shipmentTrackRepository;

    /**
     * @var TrackResource
     */
    private $trackResource;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackFactory
     */
    private $trackFactory;

    /**
     * ShipmentRepository constructor.
     * @param ShipmentApiInterface $apiAdapter
     * @param ItemRequestInterfaceFactory $requestFactory
     * @param ShipmentResponseMapper $shipmentMapper
     * @param TrackingResponseMapper $trackMapper
     * @param ShipmentReference $resource
     * @param ShipmentReferenceInterfaceFactory $shipmentReferenceFactory
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ShipmentTrackRepositoryInterface $shipmentTrackRepository
     * @param TrackResource $trackResource
     * @param TrackFactory $trackFactory
     */
    public function __construct(
        ShipmentApiInterface $apiAdapter,
        ItemRequestInterfaceFactory $requestFactory,
        ShipmentResponseMapper $shipmentMapper,
        TrackingResponseMapper $trackMapper,
        ShipmentReferenceResource $resource,
        ShipmentReferenceInterfaceFactory $shipmentReferenceFactory,
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        ShipmentTrackRepositoryInterface $shipmentTrackRepository,
        TrackResource $trackResource,
        TrackFactory $trackFactory
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->requestFactory = $requestFactory;
        $this->shipmentMapper = $shipmentMapper;
        $this->trackMapper = $trackMapper;
        $this->resource = $resource;
        $this->shipmentReferenceFactory = $shipmentReferenceFactory;
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->shipmentTrackRepository = $shipmentTrackRepository;
        $this->trackResource = $trackResource;
        $this->trackFactory = $trackFactory;
    }

    /**
     * Load external shipment entity from platform.
     *
     * @param string $shipmentId
     * @return ShipmentInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getById($shipmentId)
    {
        try {
            $request = $this->requestFactory->create(['entityId' => $shipmentId]);
            $apiShipment = $this->apiAdapter->getShipment($request);
            $shipment = $this->shipmentMapper->map($apiShipment);
        } catch (AdapterException $e) {
            if ($e->getCode() === 404) {
                throw NoSuchEntityException::singleField('shipmentId', $shipmentId);
            }

            throw new LocalizedException(__('An error occurred while loading data.'), $e);
        }

        return $shipment;
    }

    /**
     * Load external tracking info from platform using external shipment id.
     *
     * @param string $shipmentId
     * @return TrackEventInterface[]
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getTrackingById($shipmentId)
    {
        try {
            $request = $this->requestFactory->create(['entityId' => $shipmentId]);
            $apiTrackingEvents = $this->apiAdapter->getTrackingEvents($request);

            // Sort the tracking events by occurredAt descending.
            usort($apiTrackingEvents, function (TrackingEventResponseType $eventA, TrackingEventResponseType $eventB) {
                $occurredA = $eventA->getAttributes()->getOccurredAt();
                $occurredB = $eventB->getAttributes()->getOccurredAt();
                return ($occurredB - $occurredA);
            });

            $trackEvents = array_map(function (TrackingEventResponseType $apiTrackingEvent) {
                return $this->trackMapper->map($apiTrackingEvent);
            }, $apiTrackingEvents);
        } catch (AdapterException $e) {
            if ($e->getCode() === 404) {
                throw NoSuchEntityException::singleField('shipmentId', $shipmentId);
            }

            throw new LocalizedException(__('An error occurred while loading tracking history.'), $e);
        }

        return $trackEvents;
    }

    /**
     * Load external tracking info from platform using tracking number.
     *
     * @param string $trackingNumber
     * @return TrackEventInterface[]
     * @throws NoSuchEntityException
     */
    public function getTrackingByNumber($trackingNumber)
    {
        $shipmentMetadata = $this->metadataPool->getMetadata(ShipmentReferenceInterface::class);
        $connection = $this->resourceConnection->getConnection();
        /** @var  $select */
        $select = $connection
            ->select()
            ->from(['ts' => $shipmentMetadata->getEntityTable()], ShipmentReferenceInterface::EXT_SHIPMENT_ID)
            ->join(['sst' => $this->trackResource->getMainTable()], 'ts.shipment_id = sst.parent_id')
            ->where('sst.track_number = ?', $trackingNumber);

        $shipmentId = $connection->fetchOne($select);

        return $this->getTrackingById($shipmentId);
    }

    /**
     * Load local track info.
     *
     * @param string $carrierCode
     * @param string $trackingNumber
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getShipmentTrack($carrierCode, $trackingNumber)
    {
        $numberFilter = $this->filterBuilder
            ->setField('track_number')
            ->setValue($trackingNumber)
            ->setConditionType('eq')
            ->create();
        $carrierFilter = $this->filterBuilder
            ->setField('carrier_code')
            ->setValue($carrierCode)
            ->setConditionType('eq')
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter($numberFilter)
            ->addFilter($carrierFilter)
            ->addSortOrder('entity_id', SortOrder::SORT_DESC)
            ->setPageSize(1)
            ->create();

        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection $shipmentTracksCollection */
        $shipmentTracksCollection = $this->shipmentTrackRepository->getList($searchCriteria);
        /** @var \Magento\Sales\Model\Order\Shipment\Track $shipmentTrack */
        $shipmentTrack = $shipmentTracksCollection->fetchItem();

        return $shipmentTrack;
    }

    /**
     * @param ShipmentReferenceInterface $shipment
     * @return ShipmentReferenceInterface
     * @throws CouldNotSaveException
     */
    public function saveReference(ShipmentReferenceInterface $shipment)
    {
        try {
            /** @var \Temando\Shipping\Model\Shipment\ShipmentReference $shipment */
            $this->resource->save($shipment);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $shipment;
    }

    /**
     * @param int $entityId
     * @return ShipmentReferenceInterface
     * @throws NoSuchEntityException
     */
    public function getReferenceById($entityId)
    {
        /** @var \Temando\Shipping\Model\Shipment\ShipmentReference $shipment */
        $shipment = $this->shipmentReferenceFactory->create();
        $this->resource->load($shipment, $entityId);

        if (!$shipment->getId()) {
            throw new NoSuchEntityException(__('Shipment with id "%1" does not exist.', $entityId));
        }

        return $shipment;
    }

    /**
     * @param int $shipmentId
     * @return ShipmentReferenceInterface
     * @throws NoSuchEntityException
     */
    public function getReferenceByShipmentId($shipmentId)
    {
        $entityId = $this->resource->getIdByShipmentId($shipmentId);
        return $this->getReferenceById($entityId);
    }

    /**
     * Load local reference to external shipment entity by Temando shipment ID.
     *
     * @param int $extShipmentId
     *
     * @return \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReferenceByExtShipmentId($extShipmentId)
    {
        $entityId = $this->resource->getIdByExtShipmentId($extShipmentId);

        return $this->getReferenceById($entityId);
    }

    /**
     * @param string $trackingNumber
     * @return ShipmentReferenceInterface
     */
    public function getReferenceByTrackingNumber($trackingNumber)
    {
        $shipmentMetadata = $this->metadataPool->getMetadata(ShipmentReferenceInterface::class);

        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from(['ts' => $shipmentMetadata->getEntityTable()], ShipmentReferenceInterface::ENTITY_ID)
            ->join(['sst' => $this->trackResource->getMainTable()], 'ts.shipment_id = sst.parent_id')
            ->where('sst.track_number = ?', $trackingNumber);

        $entityId = $connection->fetchOne($select);

        return $this->getReferenceById($entityId);
    }
}
