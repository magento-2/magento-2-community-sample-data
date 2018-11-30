<?php
/**
 * This file is part of the Klarna Order Management module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Ordermanagement\Observer;

use Klarna\Core\Api\OrderRepositoryInterface;
use Klarna\Core\Helper\KlarnaConfig;
use Klarna\Ordermanagement\Api\ApiInterface;
use Klarna\Ordermanagement\Model\Api\Factory;
use Klarna\Ordermanagement\Model\Api\Ordermanagement;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\Data\StoreInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CancelOrder
 *
 * @package Klarna\Ordermanagement\Observer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CancelOrder implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var Ordermanagement
     */
    private $orderManagement;

    /**
     * @var KlarnaConfig
     */
    private $helper;

    /**
     * @var Factory
     */
    private $omFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    private $quoteResourceModel;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $mageOrderRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * CancelOrder constructor.
     *
     * @param LoggerInterface          $log
     * @param Ordermanagement          $om
     * @param KlarnaConfig             $helper
     * @param Factory                  $omFactory
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        LoggerInterface $log,
        Ordermanagement $orderManagement,
        KlarnaConfig $helper,
        Factory $omFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel,
        \Magento\Sales\Api\OrderRepositoryInterface $mageOrderRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->log = $log;
        $this->orderManagement = $orderManagement;
        $this->helper = $helper;
        $this->omFactory = $omFactory;
        $this->orderRepository = $orderRepository;
        $this->quoteResourceModel = $quoteResourceModel;
        $this->mageOrderRepository = $mageOrderRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $checkout_id = $observer->getKlarnaOrderId();

        $store = $this->getStore($observer);

        if ($checkout_id === null) {
            return;
        }

        $korder = $this->orderRepository->getByKlarnaOrderId($checkout_id);
        if (!$korder->getId() && !$this->helper->isDelayedPushNotification($store)) {
            // If no order exists and API does not have a delay before the push notices,
            // don't cancel.  It's likely the push happened too quickly.  See
            // LogOrderPushNotification observer
            $this->log->debug('Delaying canceling order as delayed push is enabled');
            return;
        }

        $this->cancelOrderWithKlarna($observer->getMethodCode(), $observer->getReason(), $checkout_id, $store);
        $this->cancelOrderWithMagento($observer->getOrder(), $observer->getQuote());
    }

    /**
     * @param Observer $observer
     * @return StoreInterface
     */
    private function getStore(Observer $observer)
    {
        $order = $observer->getOrder();
        if ($order) {
            return $order->getStore();
        }
        $quote = $observer->getQuote();
        if ($quote) {
            return $quote->getStore();
        }
        return $this->storeManager->getStore();
    }

    /**
     * @param string         $methodCode
     * @param string         $reason
     * @param string         $checkoutId
     * @param StoreInterface $store
     * @return void
     */
    private function cancelOrderWithKlarna($methodCode, $reason, $checkoutId, StoreInterface $store = null)
    {
        try {
            $this->getOmApi($methodCode, $store);
            $order = $this->orderManagement->getPlacedKlarnaOrder($checkoutId);
            $klarnaId = $order->getReservation();
            if (!$klarnaId) {
                $klarnaId = $checkoutId;
            }
            if ($order->getStatus() !== 'CANCELED') {
                $this->orderManagement->cancel($klarnaId);
                $this->log->debug('Canceled order with Klarna - ' . $reason);
            }
        } catch (\Exception $e) {
            $this->log->error($e);
        }
    }

    /**
     * Get api class
     *
     * @param string         $methodCode
     * @param StoreInterface $store
     * @return ApiInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOmApi($methodCode, StoreInterface $store = null)
    {
        $om_class = $this->helper->getOrderMangagementClass($store);
        /** @var ApiInterface $om */
        $this->orderManagement = $this->omFactory->create($om_class);
        $this->orderManagement->resetForStore($store, $methodCode);
        return $this->orderManagement;
    }

    /**
     * @param OrderInterface $mageOrder
     * @param Quote          $quote
     */
    private function cancelOrderWithMagento(OrderInterface $mageOrder = null, Quote $quote = null)
    {
        try {
            $debug_message = 'Magento order object not available to cancel';
            if ($mageOrder) {
                $mageOrder->cancel();
                $debug_message = 'Canceled order in Magento';
            }
            $this->log->debug($debug_message);
            if ($quote) {
                $quote->setReservedOrderId(null);
                $quote->setIsActive(1);
                // STFU and just save the quote
                $this->quoteResourceModel->save($quote->collectTotals());
            }
        } catch (\Exception $e) {
            $this->log->error($e);
        }
    }
}
