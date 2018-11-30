<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Shipment;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track as TrackResource;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentReferenceRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Shipment\ShipmentReference as ShipmentReferenceResource;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Shipment Repository
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class ShipmentReferenceRepository implements ShipmentReferenceRepositoryInterface
{
    /**
     * @var ShipmentReferenceResource
     */
    private $resource;

    /**
     * @var ShipmentReferenceInterfaceFactory
     */
    private $shipmentReferenceFactory;

    /**
     * @var ShipmentReferenceCollectionFactory
     */
    private $shipmentReferenceCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

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
     * ShipmentReferenceRepository constructor.
     * @param ShipmentReference $resource
     * @param ShipmentReferenceInterfaceFactory $shipmentReferenceFactory
     * @param ShipmentReferenceCollectionFactory $shipmentReferenceCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param ShipmentTrackRepositoryInterface $shipmentTrackRepository
     * @param TrackResource $trackResource
     */
    public function __construct(
        ShipmentReferenceResource $resource,
        ShipmentReferenceInterfaceFactory $shipmentReferenceFactory,
        ShipmentReferenceCollectionFactory $shipmentReferenceCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        ShipmentTrackRepositoryInterface $shipmentTrackRepository,
        TrackResource $trackResource
    ) {
        $this->resource = $resource;
        $this->shipmentReferenceFactory = $shipmentReferenceFactory;
        $this->shipmentReferenceCollectionFactory = $shipmentReferenceCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->shipmentTrackRepository = $shipmentTrackRepository;
        $this->trackResource = $trackResource;
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

        // builder does not get reset properly on `create()`, instantiate a fresh one…
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter($numberFilter)
            ->addFilter($carrierFilter)
            ->addSortOrder('entity_id', SortOrder::SORT_DESC)
            ->setPageSize(1)
            ->create();

        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection $shipmentTracksCollection */
        $shipmentTracksCollection = $this->shipmentTrackRepository->getList($searchCriteria);
        /** @var \Magento\Sales\Model\Order\Shipment\Track $shipmentTrack */
        $shipmentTrack = $shipmentTracksCollection->fetchItem();
        if (!$shipmentTrack) {
            throw NoSuchEntityException::singleField('track_number', $trackingNumber);
        }

        return $shipmentTrack;
    }

    /**
     * Save local reference to external shipment entity.
     *
     * @param ShipmentReferenceInterface $shipment
     * @return ShipmentReferenceInterface
     * @throws CouldNotSaveException
     */
    public function save(ShipmentReferenceInterface $shipment)
    {
        try {
            /** @var \Temando\Shipping\Model\Shipment\ShipmentReference $shipment */
            $this->resource->save($shipment);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save shipment reference.'), $exception);
        }
        return $shipment;
    }

    /**
     * Load local reference to external shipment entity.
     *
     * @param int $entityId
     * @return ShipmentReferenceInterface
     * @throws NoSuchEntityException
     */
    public function getById($entityId)
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
     * Load local reference to external shipment entity by Magento shipment ID.
     *
     * @param int $shipmentId
     * @return ShipmentReferenceInterface
     * @throws NoSuchEntityException
     */
    public function getByShipmentId($shipmentId)
    {
        $entityId = $this->resource->getIdByShipmentId($shipmentId);
        return $this->getById($entityId);
    }

    /**
     * Load local reference to external shipment entity by Temando shipment ID.
     *
     * @param string $extShipmentId
     *
     * @return \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByExtShipmentId($extShipmentId)
    {
        $entityId = $this->resource->getIdByExtShipmentId($extShipmentId);

        return $this->getById($entityId);
    }

    /**
     * Load local reference to external shipment entity by Temando return shipment ID.
     *
     * @param string $extShipmentId
     *
     * @return \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByExtReturnShipmentId($extShipmentId)
    {
        $entityId = $this->resource->getIdByExtReturnShipmentId($extShipmentId);

        return $this->getById($entityId);
    }

    /**
     * @param string $trackingNumber
     * @return ShipmentReferenceInterface
     */
    public function getByTrackingNumber($trackingNumber)
    {
        $connection = $this->resource->getConnection();
        $select = $connection
            ->select()
            ->from(['ts' => SetupSchema::TABLE_SHIPMENT], ShipmentReferenceInterface::ENTITY_ID)
            ->join(['sst' => $this->trackResource->getMainTable()], 'ts.shipment_id = sst.parent_id')
            ->where('sst.track_number = ?', $trackingNumber);

        $entityId = $connection->fetchOne($select);

        return $this->getById($entityId);
    }

    /**
     * List shipment references that match specified search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ShipmentReferenceCollection
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->shipmentReferenceCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        return $collection;
    }
}
