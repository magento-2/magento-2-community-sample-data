<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Order;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\GroupRepositoryInterface as StoreGroupRepository;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;

/**
 * View model for order related information.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderDetails implements ArgumentInterface
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

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
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * OrderDetails constructor.
     * @param TimezoneInterface $localeDate
     * @param StoreManagerInterface $storeManager
     * @param StoreGroupRepository $storeGroupRepository
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        TimezoneInterface $localeDate,
        StoreManagerInterface $storeManager,
        StoreGroupRepository $storeGroupRepository,
        WebsiteRepositoryInterface $websiteRepository,
        OrderRepositoryInterface $orderRepository,
        UrlInterface $urlBuilder
    ) {
        $this->localeDate = $localeDate;
        $this->storeManager = $storeManager;
        $this->storeGroupRepository = $storeGroupRepository;
        $this->websiteRepository = $websiteRepository;
        $this->orderRepository = $orderRepository;
        $this->urlBuilder = $urlBuilder;
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
        return $this->localeDate->getConfigTimezone(ScopeInterface::SCOPE_STORE, $order->getStoreId());
    }

    /**
     * Get object created at date
     *
     * @see \Magento\Sales\Block\Adminhtml\Order\View\Info::getOrderAdminDate
     *
     * @param string $createdAt
     * @return \DateTime
     */
    public function getOrderAdminDate($createdAt)
    {
        return $this->localeDate->date(new \DateTime($createdAt));
    }

    /**
     * @see \Magento\Sales\Block\Adminhtml\Order\View\Info::getOrderStoreName
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getOrderStoreName(OrderInterface $order)
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
    public function isSingleStoreMode()
    {
        return $this->storeManager->isSingleStoreMode();
    }

    /**
     * Obtain platform order id by given order entity.
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getExtOrderId(OrderInterface $order)
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
    public function getViewActionUrl($orderId)
    {
        return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
    }
}
