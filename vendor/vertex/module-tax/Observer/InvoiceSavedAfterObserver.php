<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ConfigurationValidator;
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

    /** @var ConfigurationValidator */
    private $configValidator;

    /** @var CountryGuard */
    private $countryGuard;

    /** @var GiftwrapExtensionLoader */
    private $extensionLoader;

    /** @var InvoiceRequestBuilder */
    private $invoiceRequestBuilder;

    /** @var InvoiceSentRegistry */
    private $invoiceSentRegistry;

    /** @var ManagerInterface */
    private $messageManager;

    /** @var TaxInvoice */
    private $taxInvoice;

    /**
     * @param Config $config
     * @param CountryGuard $countryGuard
     * @param TaxInvoice $taxInvoice
     * @param ManagerInterface $messageManager
     * @param InvoiceSentRegistry $invoiceSentRegistry
     * @param ConfigurationValidator $configValidator
     * @param InvoiceRequestBuilder $invoiceRequestBuilder
     * @param GiftwrapExtensionLoader $extensionLoader
     */
    public function __construct(
        Config $config,
        CountryGuard $countryGuard,
        TaxInvoice $taxInvoice,
        ManagerInterface $messageManager,
        InvoiceSentRegistry $invoiceSentRegistry,
        ConfigurationValidator $configValidator,
        InvoiceRequestBuilder $invoiceRequestBuilder,
        GiftwrapExtensionLoader $extensionLoader
    ) {
        $this->config = $config;
        $this->countryGuard = $countryGuard;
        $this->taxInvoice = $taxInvoice;
        $this->messageManager = $messageManager;
        $this->invoiceSentRegistry = $invoiceSentRegistry;
        $this->configValidator = $configValidator;
        $this->invoiceRequestBuilder = $invoiceRequestBuilder;
        $this->extensionLoader = $extensionLoader;
    }

    /**
     * Commit an invoice to the Vertex Tax Log
     *
     * Only when Request by Invoice Creation is turned on
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $this->extensionLoader->loadOnInvoice($invoice);

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

        /** @var boolean $configValid */
        $configValid = $this->configValidator->execute(ScopeInterface::SCOPE_STORE, $invoice->getStoreId(), true)
            ->isValid();

        if (!$isInvoiceSent && $isActive && $requestByInvoice && $canService && $configValid) {
            $request = $this->invoiceRequestBuilder->buildFromInvoice($invoice);

            /** @var boolean $sendInvoice */
            $sendInvoice = $this->taxInvoice->sendInvoiceRequest($request, $invoice->getOrder());

            if ($sendInvoice) {
                $this->invoiceSentRegistry->setInvoiceHasBeenSentToVertex($invoice);
                $this->messageManager->addSuccessMessage(__('The Vertex invoice has been sent.')->render());
            }
        }
    }
}
