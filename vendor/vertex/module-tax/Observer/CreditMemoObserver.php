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
 * Observes when a Creditmemo is issued to fire off data to the Vertex Tax Log
 */
class CreditMemoObserver implements ObserverInterface
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
     * Commit a creditmemo to the Vertex Tax Log on Creditmemo Creation
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditMemo */
        $creditMemo = $observer->getEvent()->getCreditmemo();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $creditMemo->getOrder();

        /** @var \Magento\Store\Model\Store $store */
        $store = $order->getStore();

        /** @var boolean $isActive */
        $isActive = $this->config->isVertexActive($store);

        /** @var boolean $canService */
        $canService = $this->countryGuard->isOrderServiceableByVertex($order);

        if ($isActive && $canService) {
            /** @var array $creditMemoRequestData */
            $creditMemoRequestData = $this->taxInvoice->prepareInvoiceData($creditMemo, 'refund');

            if (is_array($creditMemoRequestData) &&
                $this->taxInvoice->sendRefundRequest($creditMemoRequestData, $order)
            ) {
                $this->messageManager->addSuccessMessage(__('The Vertex invoice has been refunded.')->render());
            }
        }
    }
}
