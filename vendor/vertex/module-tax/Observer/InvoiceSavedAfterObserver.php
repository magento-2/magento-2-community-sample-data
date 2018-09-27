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
use Vertex\Tax\Model\InvoiceSentRegistry;
use Vertex\Tax\Model\TaxInvoice;

/**
 * Observes when an Invoice is issued to fire off data to the Vertex Tax Log
 */
class InvoiceSavedAfterObserver implements ObserverInterface
{
    /** @var Config */
    private $config;

    /** @var CountryGuard */
    private $countryGuard;

    /** @var TaxInvoice */
    private $taxInvoice;

    /** @var ManagerInterface */
    private $messageManager;

    /** @var InvoiceSentRegistry */
    private $invoiceSentRegistry;

    /**
     * @param Config $config
     * @param CountryGuard $countryGuard
     * @param TaxInvoice $taxInvoice
     * @param ManagerInterface $messageManager
     * @param InvoiceSentRegistry $invoiceSentRegistry
     */
    public function __construct(
        Config $config,
        CountryGuard $countryGuard,
        TaxInvoice $taxInvoice,
        ManagerInterface $messageManager,
        InvoiceSentRegistry $invoiceSentRegistry
    ) {
        $this->config = $config;
        $this->countryGuard = $countryGuard;
        $this->taxInvoice = $taxInvoice;
        $this->messageManager = $messageManager;
        $this->invoiceSentRegistry = $invoiceSentRegistry;
    }

    /**
     * Commit an invoice to the Vertex Tax Log
     *
     * Only when Request by Invoice Creation is turned on
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $invoice->getOrder();

        /** @var boolean $isInvoiceSent */
        $isInvoiceSent = $this->invoiceSentRegistry->hasInvoiceBeenSentToVertex($invoice);

        /** @var boolean $isActive */
        $isActive = $this->config->isVertexActive($invoice->getStore());

        /** @var boolean $requestByInvoice */
        $requestByInvoice = $this->config->requestByInvoiceCreation($invoice->getStore());

        /** @var boolean $canService */
        $canService = $this->countryGuard->isOrderServiceableByVertex($order);

        if (!$isInvoiceSent && $isActive && $requestByInvoice && $canService) {
            /** @var array $invoiceRequestData */
            $invoiceRequestData = $this->taxInvoice->prepareInvoiceData($invoice, 'invoice');

            /** @var boolean $sendInvoice */
            $sendInvoice = $this->taxInvoice->sendInvoiceRequest($invoiceRequestData, $invoice->getOrder());

            if (is_array($invoiceRequestData) && $sendInvoice) {
                $this->invoiceSentRegistry->setInvoiceHasBeenSentToVertex($invoice);
                $this->messageManager->addSuccessMessage(__('The Vertex invoice has been sent.')->render());
            }
        }
    }
}
