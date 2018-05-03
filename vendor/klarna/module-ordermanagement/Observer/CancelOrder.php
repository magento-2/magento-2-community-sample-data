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
    private $om;

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
        Ordermanagement $om,
        KlarnaConfig $helper,
        Factory $omFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel,
        \Magento\Sales\Api\OrderRepositoryInterface $mageOrderRepository
    ) {
        $this->log = $log;
        $this->om = $om;
        $this->helper = $helper;
        $this->omFactory = $omFactory;
        $this->orderRepository = $orderRepository;
        $this->quoteResourceModel = $quoteResourceModel;
        $this->mageOrderRepository = $mageOrderRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $checkoutId = $observer->getKlarnaOrderId();

        $store = $this->getStore($observer);

        if ($checkoutId === null) {
            return;
        }

        $kOrder = $this->orderRepository->getByKlarnaOrderId($checkoutId);
        if (!$kOrder->getId() && !$this->helper->isDelayedPushNotification($store)) {
            // If no order exists and API does not have a delay before the push notices,
            // don't cancel.  It's likely the push happened too quickly.  See
            // LogOrderPushNotification observer
            $this->log->debug('Delaying canceling order as delayed push is enabled');
            return;
        }

        $this->cancelOrderWithKlarna($observer->getMethodCode(), $observer->getReason(), $checkoutId, $store);
        $this->cancelOrderWithMagento($observer->getOrder(), $observer->getQuote());
    }

    /**
     * @param Observer $observer
     * @return StoreInterface|null
     */
    private function getStore(Observer $observer)
    {
        $order = $observer->getOrder();
        if ($order) {
            return $order->getStore();
        }
        $quote = $observer->getQuote();
        if ($quote) {
            return
                $quote->getStore();
        }
        return null;
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
            $om = $this->getOmApi($methodCode, $store);
            $order = $om->getPlacedKlarnaOrder($checkoutId);
            $klarnaId = $order->getReservation();
            if (!$klarnaId) {
                $klarnaId = $checkoutId;
            }
            if ($order->getStatus() !== 'CANCELED') {
                $om->cancel($klarnaId);
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
        $omClass = $this->helper->getOrderMangagementClass($store);
        /** @var ApiInterface $om */
        $this->om = $this->omFactory->create($omClass);
        $this->om->resetForStore($store, $methodCode);
        return $this->om;
    }

    /**
     * @param OrderInterface $mageOrder
     * @param Quote          $quote
     */
    private function cancelOrderWithMagento(OrderInterface $mageOrder, Quote $quote)
    {
        try {
            if ($mageOrder) {
                $mageOrder->cancel();
                $this->log->debug('Canceled order in Magento');
            } else {
                $this->log->debug('Magento order object not available to cancel');
            }
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
