<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\Rma;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Rma\Api\Data\RmaInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface as SalesShipmentRepositoryInterface;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Model\ResourceModel\Repository\RmaShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaShipment as RmaShipmentResource;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando RMA Shipment Repository
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class RmaShipmentRepository implements RmaShipmentRepositoryInterface
{
    /**
     * @var RmaShipmentResource
     */
    private $resource;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var SalesShipmentRepositoryInterface
     */
    private $salesShipmentRepository;

    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * RmaShipmentRepository constructor.
     *
     * @param RmaShipmentResource $resource
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param SalesShipmentRepositoryInterface $salesShipmentRepository,
     * @param RmaAccess $rmaAccess
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        RmaShipmentResource $resource,
        ShipmentRepositoryInterface $shipmentRepository,
        SalesShipmentRepositoryInterface $salesShipmentRepository,
        RmaAccess $rmaAccess,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->resource = $resource;
        $this->shipmentRepository = $shipmentRepository;
        $this->salesShipmentRepository = $salesShipmentRepository;
        $this->rmaAccess = $rmaAccess;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @return string
     */
    private function extractRmaId(SearchCriteriaInterface $criteria)
    {
        $rmaId = '';

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == 'id') {
                    $rmaId = $filter->getValue();
                } elseif ($filter->getField() == RmaShipmentResource::RMA_ID) {
                    $rmaId = $filter->getValue();
                }
            }
        }

        return $rmaId;
    }

    /**
     * Prepare filters for available return shipments:
     * - must be assigned to current RMA
     * - must have a platform reference id
     * - must not exist amongst added shipments
     *
     * @param RmaInterface $rma
     * @return \Magento\Framework\Api\Search\FilterGroup[]
     */
    private function getAvailableShipmentFilters(RmaInterface $rma)
    {
        $filterGroups = [];

        // (1) available return shipments must be assigned to any of the RMA order's shipments
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\Magento\Sales\Api\Data\ShipmentInterface::ORDER_ID, $rma->getOrderId())
            ->create();
        $searchResult = $this->salesShipmentRepository->getList($searchCriteria);
        $salesShipmentIds = array_map(function (\Magento\Sales\Api\Data\ShipmentInterface $shipment) {
            return $shipment->getEntityId();
        }, $searchResult->getItems());

        $this->filterBuilder->setField(ShipmentReferenceInterface::SHIPMENT_ID);
        $this->filterBuilder->setValue($salesShipmentIds);
        $this->filterBuilder->setConditionType('in');
        $salesShipmentIdsFilter = $this->filterBuilder->create();
        $filterGroups[]= $this->filterGroupBuilder->addFilter($salesShipmentIdsFilter)->create();

        // (2) available return shipments must have a shipment id at the platform
        $this->filterBuilder->setField(ShipmentReferenceInterface::EXT_RETURN_SHIPMENT_ID);
        $this->filterBuilder->setConditionType('notnull');
        $hasReturnShipmentFilter = $this->filterBuilder->create();
        $filterGroups[]= $this->filterGroupBuilder->addFilter($hasReturnShipmentFilter)->create();

        // (3) available return shipments must not have been added yet
        $extReturnShipmentIds = $this->resource->getShipmentIds($rma->getEntityId());
        if (!empty($extReturnShipmentIds)) {
            $this->filterBuilder->setField(ShipmentReferenceInterface::EXT_RETURN_SHIPMENT_ID);
            $this->filterBuilder->setValue($extReturnShipmentIds);
            $this->filterBuilder->setConditionType('nin');
            $notAddedFilter = $this->filterBuilder->create();
            $filterGroups[]= $this->filterGroupBuilder->addFilter($notAddedFilter)->create();
        }

        return $filterGroups;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @return ShipmentInterface[]
     */
    public function getAvailableShipments(SearchCriteriaInterface $criteria)
    {
        $rmaId = $this->extractRmaId($criteria);
        $rma = $this->rmaAccess->getById($rmaId);
        $filters = $this->getAvailableShipmentFilters($rma);

        $this->searchCriteriaBuilder->setFilterGroups($filters);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $shipmentReferenceCollection = $this->shipmentRepository->getList($searchCriteria);

        $shipmentIds = $shipmentReferenceCollection->getColumnValues('ext_return_shipment_id');
        $availableShipments = array_map(function ($shipmentId) {
            return $this->shipmentRepository->getById($shipmentId);
        }, $shipmentIds);

        return $availableShipments;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @return ShipmentInterface[]
     */
    public function getAddedShipments(SearchCriteriaInterface $criteria)
    {
        $shipments = [];

        $rmaId = $this->extractRmaId($criteria);
        $shipmentIds = $this->resource->getShipmentIds($rmaId);
        foreach ($shipmentIds as $shipmentId) {
            $shipments[] = $this->shipmentRepository->getById($shipmentId);
        }

        return $shipments;
    }

    /**
     * Save external shipment IDs to be associated with core RMA entity.
     *
     * @param int $rmaId
     * @param string[] $shipmentIds
     * @return int Number of saved shipments
     * @throws CouldNotSaveException
     */
    public function saveShipmentIds($rmaId, array $shipmentIds)
    {
        try {
            return $this->resource->saveShipmentIds($rmaId, $shipmentIds);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to assign shipments.'), $exception);
        }
    }

    /**
     * Revoke assignment of external shipment IDs with core RMA entity.
     *
     * @param int $rmaId
     * @param string[] $shipmentIds
     * @return int Number of saved shipments
     * @throws CouldNotDeleteException
     */
    public function deleteShipmentIds($rmaId, array $shipmentIds)
    {
        try {
            return $this->resource->deleteShipmentIds($rmaId, $shipmentIds);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Unable to remove shipment assignments.'), $exception);
        }
    }
}
