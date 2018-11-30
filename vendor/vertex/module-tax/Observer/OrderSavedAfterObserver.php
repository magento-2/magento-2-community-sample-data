<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ConfigurationValidator;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\Data\OrderInvoiceStatus;
use Vertex\Tax\Model\Data\OrderInvoiceStatusFactory;
use Vertex\Tax\Model\Repository\OrderInvoiceStatusRepository;
use Vertex\Tax\Model\TaxInvoice;

/**
 * Observes when an Order is saved to determine if we need to commit data to the Vertex Tax Log
 */
class OrderSavedAfterObserver implements ObserverInterface
{
    /** @var Config */
    private $config;

    /** @var ConfigurationValidator */
    private $configValidator;

    /** @var CountryGuard */
    private $countryGuard;

    /** @var GiftwrapExtensionLoader */
    private $extensionLoader;

    /** @var OrderInvoiceStatusFactory */
    private $factory;

    /** @var InvoiceRequestBuilder */
    private $invoiceRequestBuilder;

    /** @var LoggerInterface */
    private $logger;

    /** @var ManagerInterface */
    private $messageManager;
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderInvoiceStatusRepository */
    private $repository;

    /** @var TaxInvoice */
    private $taxInvoice;

    /**
     * @param Config $config
     * @param CountryGuard $countryGuard
     * @param TaxInvoice $taxInvoice
     * @param ManagerInterface $messageManager
     * @param OrderInvoiceStatusRepository $repository
     * @param OrderInvoiceStatusFactory $factory
     * @param LoggerInterface $logger
     * @param ConfigurationValidator $configValidator
     * @param InvoiceRequestBuilder $invoiceRequestBuilder
     * @param GiftwrapExtensionLoader $extensionLoader
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Config $config,
        CountryGuard $countryGuard,
        TaxInvoice $taxInvoice,
        ManagerInterface $messageManager,
        OrderInvoiceStatusRepository $repository,
        OrderInvoiceStatusFactory $factory,
        LoggerInterface $logger,
        ConfigurationValidator $configValidator,
        InvoiceRequestBuilder $invoiceRequestBuilder,
        GiftwrapExtensionLoader $extensionLoader,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->config = $config;
        $this->countryGuard = $countryGuard;
        $this->taxInvoice = $taxInvoice;
        $this->messageManager = $messageManager;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->logger = $logger;
        $this->configValidator = $configValidator;
        $this->invoiceRequestBuilder = $invoiceRequestBuilder;
        $this->extensionLoader = $extensionLoader;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Commit an Invoice to the Vertex Tax Log
     *
     * When an order is saved, request by order status is enabled, and the Order's status is the one configured, we
     * will commit it's data to the Vertex Tax Log
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var boolean $isActive */
        $isActive = $this->config->isVertexActive($order->getStore());

        if ($this->hasInvoice($order->getId())) {
            // Exit out early if an Invoice has already be lodged for this Order as a whole
            return $this;
        }

        /** @var boolean $requestByOrder */
        $requestByOrder = $this->requestByOrderStatus($order->getStatus(), $order->getStore());

        /** @var boolean $canService */
        $canService = $this->countryGuard->isOrderServiceableByVertex($order);

        /** @var boolean $configValid */
        $configValid = $this->configValidator->execute(ScopeInterface::SCOPE_STORE, $order->getStoreId(), true)
            ->isValid();

        if ($isActive && $requestByOrder && $canService && $configValid) {
            $extensionAttributes = $order->getExtensionAttributes();
            if ($extensionAttributes === null || !$extensionAttributes->getShippingAssignments()) {
                $this->loadShippingAssignments($order);
            }
            $this->extensionLoader->loadOnOrder($order);
            $request = $this->invoiceRequestBuilder->buildFromOrder($order);
            if ($this->taxInvoice->sendInvoiceRequest($request, $order)) {
                $this->messageManager->addSuccessMessage(__('The Vertex invoice has been sent.')->render());
                $this->setHasInvoice($order->getId());
            }
        }

        return $this;
    }

    /**
     * Determine if an Order already has an invoice
     *
     * @param int $orderId
     * @return bool
     */
    private function hasInvoice($orderId)
    {
        try {
            $this->repository->getByOrderId($orderId);
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * Load shipping assignments through Magento
     *
     * None of the builders are public API, so this is the route we have to take to make Magento load the shipping
     * assignments into the extension attributes if it is not already.
     *
     * @param Order $order
     * @return void
     */
    private function loadShippingAssignments(Order $order)
    {
        $loadedOrder = $this->orderRepository->get($order->getEntityId());
        if ($order->getExtensionAttributes() === null) {
            $order->setExtensionAttributes($loadedOrder->getExtensionAttributes());
        } elseif ($loadedOrder->getExtensionAttributes() !== null) {
            $order->getExtensionAttributes()->setShippingAssignments(
                $loadedOrder->getExtensionAttributes()->getShippingAssignments()
            );
        }
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

    /**
     * Register that an Order already has an Invoice
     *
     * @param int $orderId
     * @return void
     */
    private function setHasInvoice($orderId)
    {
        /** @var OrderInvoiceStatus $orderInvoiceStatus */
        try {
            $orderInvoiceStatus = $this->repository->getByOrderId($orderId);
        } catch (NoSuchEntityException $e) {
            $orderInvoiceStatus = $this->factory->create();
            $orderInvoiceStatus->setId($orderId);
        }
        $orderInvoiceStatus->setIsSent(true);
        try {
            $this->repository->save($orderInvoiceStatus);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }
    }
}
