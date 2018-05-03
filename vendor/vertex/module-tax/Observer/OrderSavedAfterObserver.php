<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\TaxInvoice;

/**
 * Observes when an Order is saved to determine if we need to commit data to the Vertex Tax Log
 */
class OrderSavedAfterObserver implements ObserverInterface
{
    /** @var Config */
    private $config;

    /** @var CountryGuard */
    private $countryGuard;

    /** @var TaxInvoice */
    private $taxInvoice;

    /** @var ManagerInterface */
    private $messageManager;

    /**
     * @param Config $config
     * @param CountryGuard $countryGuard
     * @param TaxInvoice $taxInvoice
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Config $config,
        CountryGuard $countryGuard,
        TaxInvoice $taxInvoice,
        ManagerInterface $messageManager
    ) {
        $this->config = $config;
        $this->countryGuard = $countryGuard;
        $this->taxInvoice = $taxInvoice;
        $this->messageManager = $messageManager;
    }

    /**
     * Commit an Invoice to the Vertex Tax Log
     *
     * When an order is saved, request by order status is enabled, and the Order's status is the one configured, we
     * will commit it's data to the Vertex Tax Log
     *
     * @param Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var boolean $isActive */
        $isActive = $this->config->isVertexActive($order->getStore());

        /** @var boolean $requestByOrder */
        $requestByOrder = $this->requestByOrderStatus($order->getStatus(), $order->getStore());

        /** @var boolean $canService */
        $canService = $this->countryGuard->isOrderServiceableByVertex($order);

        if ($isActive && $requestByOrder && $canService) {
            /** @var array $invoiceRequestData */
            $invoiceRequestData = $this->taxInvoice->prepareInvoiceData($order);
            if (is_array($invoiceRequestData) && $this->taxInvoice->sendInvoiceRequest($invoiceRequestData, $order)) {
                $this->messageManager->addSuccessMessage(__('The Vertex invoice has been sent.')->render());
            }
        }

        return $this;
    }

    /**
     * Determine if we should commit to the tax log on this order status
     *
     * Checks if request by order status is enabled and that our status matches the one configured
     *
     * @param string $status
     * @param string|null $store
     * @return bool
     */
    private function requestByOrderStatus($status, $store = null)
    {
        return $this->config->requestByOrderStatus($store) && $status === $this->config->invoiceOrderStatus($store);
    }
}
