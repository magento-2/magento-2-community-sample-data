<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Pickup\Grid;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\Model\ResourceModel\Pickup\PickupCompositeFactory;
use Temando\Shipping\Model\ResourceModel\Order\OrderReference as OrderReferenceResource;
use Temando\Shipping\Model\ResourceModel\Repository\PickupRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Webservice\Collection as ApiCollection;

/**
 * Temando Pickup Fulfillment Resource Collection
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Collection extends ApiCollection
{
    /**
     * @var PickupRepositoryInterface
     */
    private $pickupRepository;

    /**
     * @var OrderReferenceResource
     */
    private $orderReferenceResource;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PickupCompositeFactory
     */
    private $pickupCompositeFactory;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param ManagerInterface $messageManager
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PickupRepositoryInterface $pickupRepository
     * @param OrderReferenceResource $orderReferenceResource
     * @param OrderRepositoryInterface $orderRepository
     * @param PickupCompositeFactory $pickupCompositeFactory
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ManagerInterface $messageManager,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PickupRepositoryInterface $pickupRepository,
        OrderReferenceResource $orderReferenceResource,
        OrderRepositoryInterface $orderRepository,
        PickupCompositeFactory $pickupCompositeFactory
    ) {
        $this->pickupRepository = $pickupRepository;
        $this->orderReferenceResource = $orderReferenceResource;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->pickupCompositeFactory = $pickupCompositeFactory;

        parent::__construct($entityFactory, $messageManager, $filterBuilder, $searchCriteriaBuilder);
    }
    /**
     * @param SearchCriteriaInterface $criteria
     * @return PickupInterface[]
     */
    public function fetchData(SearchCriteriaInterface $criteria)
    {
        $pickups = $this->pickupRepository->getList($criteria);

        // join local order data with platform data
        $orderIds = [];
        foreach ($pickups as $pickup) {
            $pickupId = $pickup->getPickupId();
            $orderId = $this->orderReferenceResource->getOrderIdByExtOrderId($pickup->getOrderId());

            $orderIds[$pickupId] = $orderId;
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderInterface::ENTITY_ID, $orderIds, 'in')
            ->create();
        $orders = $this->orderRepository->getList($searchCriteria);

        $pickupComposite = $this->pickupCompositeFactory->create(['orderSearchResult' => $orders]);
        $pickups = array_map(function (PickupInterface $pickup) use ($pickupComposite, $orderIds) {
            return $pickupComposite->aggregate($pickup, $orderIds[$pickup->getPickupId()]);
        }, $pickups);

        // remove all pickups which do not exist locally
        $pickups = array_filter($pickups, function (PickupInterface $pickup) {
            return $pickup->getSalesOrderId();
        });

        return $pickups;
    }
}
