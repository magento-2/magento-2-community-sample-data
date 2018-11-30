<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Pickup;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface as SalesOrderRepositoryInterface;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\Model\PickupProviderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\PickupRepositoryInterface;

/**
 * Temando Pickup Loader
 *
 * Load pickup fulfillments by their ID or the associated order ID.
 * Convenience wrapper around the pickup and order repositories.
 *
 * @package Temando\Shipping\Model
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupLoader
{
    /**
     * @var PickupRepositoryInterface
     */
    private $pickupRepository;

    /**
     * @var PickupProviderInterface
     */
    private $pickupProvider;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SalesOrderRepositoryInterface
     */
    private $salesOrderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param PickupRepositoryInterface $pickupRepository
     * @param PickupProviderInterface $pickupProvider
     * @param OrderRepositoryInterface $orderRepository
     * @param SalesOrderRepositoryInterface $salesOrderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        PickupRepositoryInterface $pickupRepository,
        PickupProviderInterface $pickupProvider,
        OrderRepositoryInterface $orderRepository,
        SalesOrderRepositoryInterface $salesOrderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->pickupRepository = $pickupRepository;
        $this->pickupProvider = $pickupProvider;
        $this->orderRepository = $orderRepository;
        $this->salesOrderRepository = $salesOrderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param int $salesOrderId
     * @return OrderInterface|null
     */
    private function loadOrderById(int $salesOrderId): ?OrderInterface
    {
        try {
            $order = $this->salesOrderRepository->get($salesOrderId);
            return $order;
        } catch (LocalizedException $exception) {
            return null;
        }
    }

    /**
     * @param string $extOrderId
     * @return OrderInterface|null
     */
    private function loadOrderByExtOrderId(string $extOrderId): ?OrderInterface
    {
        try {
            $orderReference = $this->orderRepository->getReferenceByExtOrderId($extOrderId);
            $order = $this->salesOrderRepository->get($orderReference->getOrderId());
            return $order;
        } catch (LocalizedException $exception) {
            return null;
        }
    }

    /**
     * @param string $pickupId
     * @return PickupInterface|null
     */
    private function loadPickupById(string $pickupId): ?PickupInterface
    {
        try {
            return $this->pickupRepository->getById($pickupId);
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * @param int $salesOrderId
     * @return PickupInterface[]
     */
    private function loadPickupsByOrderId(int $salesOrderId): array
    {
        $this->searchCriteriaBuilder->addFilter(PickupInterface::ORDER_ID, $salesOrderId);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $pickups = $this->pickupRepository->getList($searchCriteria);

        $keys = array_map(function (PickupInterface $pickup) {
            return $pickup->getPickupId();
        }, $pickups);

        return array_combine($keys, $pickups);
    }

    /**
     * Returns Pickup List for a specific order or pickup id
     *
     * @param int $salesOrderId
     * @param string $pickupId
     * @return PickupInterface[]
     */
    public function load(int $salesOrderId = 0, string $pickupId = ''): array
    {
        $pickups = [];

        if ($salesOrderId) {
            // load all pickup fulfillments associated to a given order
            $pickups = $this->loadPickupsByOrderId($salesOrderId);
        }

        if ($pickupId && !$salesOrderId) {
            // load one pickup by given ID
            $pickup = $this->loadPickupById($pickupId);
            $pickups = [$pickupId => $pickup];
        }

        return $pickups;
    }

    /**
     * @param PickupInterface[] $pickups
     * @param int $salesOrderId
     * @param string $pickupId
     */
    public function register(array $pickups, int $salesOrderId = 0, string $pickupId = ''): void
    {
        // register loaded pickups
        if (!empty($pickups)) {
            $this->pickupProvider->setPickups($pickups);

            if ($pickupId && isset($pickups[$pickupId])) {
                // add primary pickup if one was requested via ID
                $this->pickupProvider->setPickup($pickups[$pickupId]);
            }
        }

        // register loaded order
        if ($salesOrderId) {
            $order = $this->loadOrderById($salesOrderId);
            $this->pickupProvider->setOrder($order);
        } elseif ($pickupId && isset($pickups[$pickupId])) {
            $order = $this->loadOrderByExtOrderId($pickups[$pickupId]->getOrderId());
            $this->pickupProvider->setOrder($order);
        }
    }
}
