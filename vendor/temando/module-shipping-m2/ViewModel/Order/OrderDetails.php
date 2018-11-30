<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Api\GroupRepositoryInterface as StoreGroupRepository;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\ViewModel\DataProvider\DeliveryType;
use Temando\Shipping\ViewModel\DataProvider\OrderDate;
use Temando\Shipping\ViewModel\DataProvider\OrderUrl;

/**
 * View model for order related information.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderDetails implements ArgumentInterface
{
    /**
     * @var OrderDate
     */
    private $orderDate;

    /**
     * @var OrderUrl
     */
    private $orderUrl;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreGroupRepository
     */
    private $storeGroupRepository;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var DeliveryType
     */
    private $deliveryType;

    /**
     * OrderDetails constructor.
     * @param OrderDate $orderDate
     * @param OrderUrl $orderUrl
     * @param StoreManagerInterface $storeManager
     * @param StoreGroupRepository $storeGroupRepository
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param DeliveryType $orderType
     */
    public function __construct(
        OrderDate $orderDate,
        OrderUrl $orderUrl,
        StoreManagerInterface $storeManager,
        StoreGroupRepository $storeGroupRepository,
        WebsiteRepositoryInterface $websiteRepository,
        OrderRepositoryInterface $orderRepository,
        DeliveryType $orderType
    ) {
        $this->orderDate = $orderDate;
        $this->orderUrl = $orderUrl;
        $this->storeManager = $storeManager;
        $this->storeGroupRepository = $storeGroupRepository;
        $this->websiteRepository = $websiteRepository;
        $this->orderRepository = $orderRepository;
        $this->deliveryType = $orderType;
    }

    /**
     * Get timezone for store
     *
     * @see \Magento\Sales\Block\Adminhtml\Order\View\Info::getTimezoneForStore
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getOrderTimezone(OrderInterface $order)
    {
        return $this->orderDate->getStoreTimezone((int)$order->getStoreId());
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    public function getOrderAdminDate(OrderInterface $order): string
    {
        $date = $order->getCreatedAt();
        return $this->orderDate->getAdminDate($date);
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    public function getOrderStoreDate(OrderInterface $order): string
    {
        $date = $order->getCreatedAt();
        $storeId = (int)$order->getStoreId();
        return $this->orderDate->getStoreDate($date, $storeId);
    }

    /**
     * @see \Magento\Sales\Block\Adminhtml\Order\View\Info::getOrderStoreName
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getOrderStoreName(OrderInterface $order): string
    {
        $storeId = $order->getStoreId();
        if ($storeId === null) {
            $deleted = __(' [deleted]');
            return nl2br($order->getStoreName()) . $deleted;
        }

        $store = $this->storeManager->getStore($storeId);
        $group = $this->storeGroupRepository->get($store->getStoreGroupId());
        $website = $this->websiteRepository->getById($store->getWebsiteId());

        $name = [$website->getName(), $group->getName(), $store->getName()];
        return implode('<br/>', $name);
    }

    /**
     * Is single store mode
     *
     * @see \Magento\Sales\Block\Adminhtml\Order\View\Info::isSingleStoreMode
     *
     * @return bool
     */
    public function isSingleStoreMode(): bool
    {
        return $this->storeManager->isSingleStoreMode();
    }

    /**
     * Obtain platform order id by given order entity.
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getExtOrderId(OrderInterface $order): string
    {
        try {
            /** @var \Temando\Shipping\Api\Data\Order\OrderReferenceInterface $orderReference */
            $orderReference = $this->orderRepository->getReferenceByOrderId($order->getEntityId());

            return $orderReference->getExtOrderId();
        } catch (NoSuchEntityException $noSuchEntityException) {
            return '';
        }
    }

    /**
     * @param string $orderId
     *
     * @return string
     */
    public function getViewActionUrl(string $orderId): string
    {
        return $this->orderUrl->getViewActionUrl(['order_id' => $orderId]);
    }

    /**
     * @param OrderInterface|Order $order
     * @return bool
     */
    public function isPickupOrder(OrderInterface $order): bool
    {
        return $this->deliveryType->isPickupOrder($order);
    }
}
