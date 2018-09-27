<?php
/**
 * This file is part of the Klarna Order Management module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Ordermanagement\Controller\Api;

use Klarna\Core\Exception as KlarnaException;
use Klarna\Core\Helper\ConfigHelper;
use Klarna\Core\Model\OrderRepository;
use Klarna\Ordermanagement\Model\Api\Ordermanagement;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Webapi\Exception as WebException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\OrderRepository as MageOrderRepository;
use Psr\Log\LoggerInterface;

/**
 * Order update from pending status
 *
 * @package Klarna\Ordermanagement\Controller\Api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Notification extends Action
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * Magento Order Repository
     *
     * @var MageOrderRepository
     */
    private $mageOrderRepository;
    /**
     * Klarna Order Repository
     *
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * Notification constructor.
     *
     * @param Context             $context
     * @param LoggerInterface     $logger
     * @param JsonFactory         $resultJsonFactory
     * @param OrderRepository     $orderRepository
     * @param MageOrderRepository $mageOrderRepository
     * @param ConfigHelper        $configHelper
     * @param DataObjectFactory   $dataObjectFactory
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        JsonFactory $resultJsonFactory,
        OrderRepository $orderRepository,
        MageOrderRepository $mageOrderRepository,
        ConfigHelper $configHelper,
        DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->mageOrderRepository = $mageOrderRepository;
        $this->orderRepository = $orderRepository;
        $this->configHelper = $configHelper;
        $this->logger = $logger;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            $resultPage = $this->resultJsonFactory->create();
            $resultPage->setHttpResponseCode(404);
            return $resultPage;
        }

        $checkoutId = $this->getRequest()->getParam('id');

        try {
            $body = $this->getRequest()->getContent();

            $notification = json_decode($body, true);
            $notification = $this->dataObjectFactory->create(['data' => $notification]);

            if (null === $checkoutId) {
                $checkoutId = $notification->getOrderId();
            }

            /** @var \Klarna\Core\Model\Order $klarnaOrder */
            $klarnaOrder = $this->orderRepository->getByKlarnaOrderId($checkoutId);
            /** @var Order $order */
            $order = $this->mageOrderRepository->get($klarnaOrder->getOrderId());

            if (!$order->getId()) {
                throw new KlarnaException(__('Order not found'));
            }

            /** @var Payment $payment */
            $payment = $order->getPayment();

            switch ($notification->getEventType()) {
                case Ordermanagement::ORDER_NOTIFICATION_FRAUD_STOPPED:
                    // Intentionally fall through as logic is the same
                    $order->addStatusHistoryComment(__('Suspected Fraud: DO NOT SHIP. If already shipped, 
                    please attempt to stop the carrier from delivering.'));
                    $payment->setNotificationResult(true);
                    $payment->setIsFraudDetected(true);
                    $payment->deny(false);
                    break;
                case Ordermanagement::ORDER_NOTIFICATION_FRAUD_REJECTED:
                    $payment->setNotificationResult(true);
                    $payment->setIsFraudDetected(true);
                    $payment->deny(false);
                    break;
                case Ordermanagement::ORDER_NOTIFICATION_FRAUD_ACCEPTED:
                    $payment->setNotificationResult(true);
                    $payment->accept(false);
                    $this->setOrderStatus($order, $payment->getMethod());
                    break;
            }
            $this->mageOrderRepository->save($order);
        } catch (\Exception $e) {
            $this->logger->error($e);
            $resultPage = $this->resultJsonFactory->create();
            $resultPage->setHttpResponseCode(500);
            $resultPage->setJsonData(
                json_encode([
                    'error'   => 400,
                    'message' => $e->getMessage(),
                ])
            );
            return $resultPage;
        }
        $resultPage = $this->resultJsonFactory->create();
        $resultPage->setHttpResponseCode(200);
        $resultPage->setData([]);
        return $resultPage;
    }

    /**
     * Update the order status if the order state is Order::STATE_PROCESSING
     *
     * @param Order  $order
     * @param String $method
     * @param string $status
     */
    public function setOrderStatus($order, $method, $status = null)
    {
        if (!isset($status)) {
            $status = $this->configHelper->getProcessedOrderStatus($order->getStore(), $method);
        }

        if (Order::STATE_PROCESSING === $order->getState()) {
            $order->addStatusHistoryComment(__('Order processed by Klarna.'), $status);
        }
    }
}
