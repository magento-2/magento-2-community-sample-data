<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\Rma;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaShipment as RmaShipmentResource;
use Temando\Shipping\Model\ResourceModel\Repository\RmaShipmentRepositoryInterface;
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
     * RmaShipmentRepository constructor.
     *
     * @param RmaShipmentResource $resource
     * @param ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        RmaShipmentResource $resource,
        ShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->resource = $resource;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Query all external shipment IDs based on given search criteria.
     *
     * @param SearchCriteriaInterface $criteria
     *
     * @return string[]
     */
    public function getShipmentIds(SearchCriteriaInterface $criteria)
    {
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == 'id') {
                    return $this->resource->getShipmentIds($filter->getValue());
                } elseif ($filter->getField() == RmaShipmentResource::RMA_ID) {
                    return $this->resource->getShipmentIds($filter->getValue());
                } elseif ($filter->getField() == RmaShipmentResource::RMA_SHIPMENT_ID) {
                    return [$filter->getValue()];
                }
            }
        }

        return [];
    }

    /**
     * Load RMA shipments from platform based on given search criteria.
     *
     * @param SearchCriteriaInterface $criteria
     * @return ShipmentInterface[]
     */
    public function getShipments(SearchCriteriaInterface $criteria)
    {
        $shipments = [];
        $shipmentIds = $this->getShipmentIds($criteria);

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
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }
}
