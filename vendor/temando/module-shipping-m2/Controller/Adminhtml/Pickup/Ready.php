<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Pickup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Temando\Shipping\Model\Pickup\Email\Sender\PickupSender;
use Temando\Shipping\Model\Pickup\PickupLoader;
use Temando\Shipping\Model\Pickup\PickupManagementFactory;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\Model\PickupInterfaceFactory;
use Temando\Shipping\Model\PickupProviderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderPickupLocationRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\PickupRepositoryInterface;

/**
 * Mark Pickup Ready Action
 *
 * @package Temando\Shipping\Controller
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Ready extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::pickups';

    /**
     * @var PickupLoader
     */
    private $pickupLoader;

    /**
     * @var PickupManagementFactory
     */
    private $pickupManagementFactory;

    /**
     * @var PickupProviderInterface
     */
    private $pickupProvider;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PickupRepositoryInterface
     */
    private $pickupRepository;

    /**
     * @var OrderPickupLocationRepositoryInterface
     */
    private $pickupLocationRepository;

    /**
     * @var PickupInterfaceFactory
     */
    private $pickupFactory;

    /**
     * @var PickupSender
     */
    private $pickupSender;

    /**
     * Ready constructor.
     * @param Context $context
     * @param PickupLoader $pickupLoader
     * @param PickupManagementFactory $pickupManagementFactory
     * @param PickupProviderInterface $pickupProvider
     * @param OrderRepositoryInterface $orderRepository
     * @param PickupRepositoryInterface $pickupRepository
     * @param OrderPickupLocationRepositoryInterface $pickupLocationRepository
     * @param PickupInterfaceFactory $pickupFactory
     * @param PickupSender $pickupSender
     */
    public function __construct(
        Context $context,
        PickupLoader $pickupLoader,
        PickupManagementFactory $pickupManagementFactory,
        PickupProviderInterface $pickupProvider,
        OrderRepositoryInterface $orderRepository,
        PickupRepositoryInterface $pickupRepository,
        OrderPickupLocationRepositoryInterface $pickupLocationRepository,
        PickupInterfaceFactory $pickupFactory,
        PickupSender $pickupSender
    ) {
        $this->pickupLoader = $pickupLoader;
        $this->pickupManagementFactory = $pickupManagementFactory;
        $this->pickupProvider = $pickupProvider;
        $this->orderRepository = $orderRepository;
        $this->pickupRepository = $pickupRepository;
        $this->pickupLocationRepository = $pickupLocationRepository;
        $this->pickupFactory = $pickupFactory;
        $this->pickupSender = $pickupSender;

        parent::__construct($context);
    }

    /**
     * Update pickup item quantities, pickup status, and send customer confirmation email.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        // load pickup data
        $pickupData = $this->getRequest()->getParam('pickup');
        $requestedItems = $pickupData['items'];

        $orderId = $this->getRequest()->getParam('sales_order_id', 0);
        $pickupId = $this->getRequest()->getParam('pickup_id', '');

        $pickups = $this->pickupLoader->load($orderId, $pickupId);
        $this->pickupLoader->register($pickups, $orderId, $pickupId);

        /** @var \Temando\Shipping\Model\Pickup $pickup */
        $pickup = $this->pickupProvider->getPickup();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->pickupProvider->getOrder();

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);

        // perform sanity checks
        if (!$order->canShip()) {
            $this->messageManager->addErrorMessage('Order cannot be fulfilled.');
            return $resultRedirect;
        }

        $pickupManagement = $this->pickupManagementFactory->create([
            'pickups' => $pickups,
        ]);
        $requestedItems = $pickupManagement->getRequestedItems($requestedItems, $order->getAllVisibleItems());
        if (empty($requestedItems)) {
            $this->messageManager->addErrorMessage('No items available to fulfill.');
            return $resultRedirect;
        }

        try {
            $pickup->setData(PickupInterface::ITEMS, $requestedItems);
            $pickup->setData(PickupInterface::STATE, PickupInterface::STATE_READY);
            $this->pickupRepository->save($pickup);
            $this->messageManager->addSuccessMessage('Pickup is ready for collection.');
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage('Failed to update pickup status.');
            return $resultRedirect;
        }

        try {
            // update order status
            $order->setIsInProcess(true);
            $this->orderRepository->save($order);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage('Failed to update order items.');
        }

        // send ready email
        try {
            $this->pickupSender->setPickupReady();
            $this->pickupSender->send($order);
            $this->messageManager->addSuccessMessage('Email confirmation was sent.');
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage('Email confirmation could not be sent.');
        }

        $remainingItems = $pickupManagement->getOpenItems($order->getAllVisibleItems());
        if (empty($remainingItems)) {
            // done
            return $resultRedirect;
        }

        try {
            // create new pickup at platform
            $addressId = $order->getShippingAddress()->getId();
            $pickupLocation = $this->pickupLocationRepository->get($addressId);
            $newPickup = $this->pickupFactory->create(['data' => [
                PickupInterface::LOCATION_ID => $pickupLocation->getPickupLocationId(),
                PickupInterface::ORDER_ID => $pickup->getOrderId(),
                PickupInterface::ORDER_REFERENCE => $pickup->getOrderReference() ?: $order->getIncrementId(),
                PickupInterface::ITEMS => $remainingItems,
            ]]);
            $this->pickupRepository->save($newPickup);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage('Failed to create a new pickup fulfillment.');
        }

        return $resultRedirect;
    }
}
