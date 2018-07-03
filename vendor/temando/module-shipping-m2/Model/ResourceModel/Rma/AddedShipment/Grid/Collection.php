<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Rma\AddedShipment\Grid;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Temando\Shipping\Model\ResourceModel\Repository\RmaShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Webservice\Collection as ApiCollection;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando RMA Added Return Shipment Resource Collection
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Collection extends ApiCollection
{
    /**
     * @var RmaShipmentRepositoryInterface
     */
    private $rmaShipmentRepository;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param ManagerInterface $messageManager
     * @param RmaShipmentRepositoryInterface $rmaShipmentRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ManagerInterface $messageManager,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RmaShipmentRepositoryInterface $rmaShipmentRepository
    ) {
        $this->rmaShipmentRepository = $rmaShipmentRepository;

        parent::__construct($entityFactory, $messageManager, $filterBuilder, $searchCriteriaBuilder);
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @return ShipmentInterface[]
     */
    public function fetchData(SearchCriteriaInterface $criteria)
    {
        $shipments = $this->rmaShipmentRepository->getAddedShipments($criteria);

        return $shipments;
    }
}
