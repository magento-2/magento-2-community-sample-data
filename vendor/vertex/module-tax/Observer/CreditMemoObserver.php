<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ConfigurationValidator;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\TaxInvoice;

/**
 * Observes when a Creditmemo is issued to fire off data to the Vertex Tax Log
 */
class CreditMemoObserver implements ObserverInterface
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

    /** @var ManagerInterface */
    private $messageManager;

    /** @var TaxInvoice */
    private $taxInvoice;

    /**
     * @param Config $config
     * @param CountryGuard $countryGuard
     * @param TaxInvoice $taxInvoice
     * @param ManagerInterface $messageManager
     * @param ConfigurationValidator $configValidator
     * @param InvoiceRequestBuilder $invoiceRequestBuilder
     * @param GiftwrapExtensionLoader $extensionLoader
     */
    public function __construct(
        Config $config,
        CountryGuard $countryGuard,
        TaxInvoice $taxInvoice,
        ManagerInterface $messageManager,
        ConfigurationValidator $configValidator,
        InvoiceRequestBuilder $invoiceRequestBuilder,
        GiftwrapExtensionLoader $extensionLoader
    ) {
        $this->config = $config;
        $this->countryGuard = $countryGuard;
        $this->taxInvoice = $taxInvoice;
        $this->messageManager = $messageManager;
        $this->configValidator = $configValidator;
        $this->invoiceRequestBuilder = $invoiceRequestBuilder;
        $this->extensionLoader = $extensionLoader;
    }

    /**
     * Commit a creditmemo to the Vertex Tax Log on Creditmemo Creation
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Creditmemo $creditMemo */
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $this->extensionLoader->loadOnCreditmemo($creditMemo);

        /** @var Order $order */
        $order = $creditMemo->getOrder();

        /** @var Store $store */
        $store = $order->getStore();

        /** @var boolean $isActive */
        $isActive = $this->config->isVertexActive($store);

        /** @var boolean $canService */
        $canService = $this->countryGuard->isOrderServiceableByVertex($order);

        /** @var boolean $configValid */
        $configValid = $this->configValidator->execute(ScopeInterface::SCOPE_STORE, $creditMemo->getStoreId(), true)
            ->isValid();

        if ($isActive && $canService && $configValid) {
            $request = $this->invoiceRequestBuilder->buildFromCreditmemo($creditMemo);

            if ($this->taxInvoice->sendRefundRequest($request, $order)) {
                $this->messageManager->addSuccessMessage(__('The Vertex invoice has been refunded.')->render());
            }
        }
    }
}
