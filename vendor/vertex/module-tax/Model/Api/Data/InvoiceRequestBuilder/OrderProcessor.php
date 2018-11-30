<?php

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Services\Invoice\RequestInterfaceFactory;
use Vertex\Tax\Model\Api\Data\CustomerBuilder;
use Vertex\Tax\Model\Api\Data\SellerBuilder;
use Vertex\Tax\Model\Api\Utility\DeliveryTerm;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\DateTimeImmutableFactory;

/**
 * Processes a Magento Order and returns a Vertex Invoice Request
 */
class OrderProcessor
{
    /** @var Config */
    private $config;

    /** @var CustomerBuilder */
    private $customerBuilder;

    /** @var DateTimeImmutableFactory */
    private $dateTimeFactory;

    /** @var DeliveryTerm */
    private $deliveryTerm;

    /** @var OrderProcessorInterface */
    private $processorPool;

    /** @var RequestInterfaceFactory */
    private $requestFactory;

    /** @var SellerBuilder */
    private $sellerBuilder;

    /**
     * @param RequestInterfaceFactory $requestFactory
     * @param DateTimeImmutableFactory $dateTimeFactory
     * @param SellerBuilder $sellerBuilder
     * @param CustomerBuilder $customerBuilder
     * @param DeliveryTerm $deliveryTerm
     * @param Config $config
     * @param OrderProcessorInterface $processorPool
     */
    public function __construct(
        RequestInterfaceFactory $requestFactory,
        DateTimeImmutableFactory $dateTimeFactory,
        SellerBuilder $sellerBuilder,
        CustomerBuilder $customerBuilder,
        DeliveryTerm $deliveryTerm,
        Config $config,
        OrderProcessorInterface $processorPool
    ) {
        $this->requestFactory = $requestFactory;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->sellerBuilder = $sellerBuilder;
        $this->customerBuilder = $customerBuilder;
        $this->deliveryTerm = $deliveryTerm;
        $this->config = $config;
        $this->processorPool = $processorPool;
    }

    /**
     * Create a Vertex Invoice Request from a Magento Order
     *
     * @param OrderInterface $invoice
     * @return RequestInterface
     */
    public function process(OrderInterface $order)
    {
        $address = $order->getIsVirtual() ? $order->getBillingAddress() : $this->getShippingFromOrder($order);

        $scopeCode = $order->getStoreId();

        $seller = $this->sellerBuilder
            ->setScopeType(ScopeInterface::SCOPE_STORE)
            ->setScopeCode($scopeCode)
            ->build();

        $customer = $this->customerBuilder->buildFromOrderAddress(
            $address,
            $address !== null ? $address->getCustomerId() : null,
            $order->getCustomerGroupId(),
            $scopeCode
        );

        $request = $this->requestFactory->create();
        $request->setDocumentNumber($order->getIncrementId());
        $request->setDocumentDate($this->dateTimeFactory->create());
        $request->setTransactionType(RequestInterface::TRANSACTION_TYPE_SALE);
        $request->setSeller($seller);
        $request->setCustomer($customer);
        $this->deliveryTerm->addIfApplicable($request);

        if ($this->config->getLocationCode($scopeCode)) {
            $request->setLocationCode($this->config->getLocationCode($scopeCode));
        }

        $request = $this->processorPool->process($request, $order);

        return $request;
    }

    /**
     * Retrieve the shipping address from an Order
     *
     * @param OrderInterface $order
     * @return OrderAddressInterface|null
     */
    private function getShippingFromOrder(OrderInterface $order)
    {
        if ($order instanceof Order && $order->getShippingAddress()) {
            return $order->getShippingAddress();
        }

        return $order->getExtensionAttributes() !== null
        && $order->getExtensionAttributes()->getShippingAssignments()
        && $order->getExtensionAttributes()->getShippingAssignments()[0]
        && $order->getExtensionAttributes()->getShippingAssignments()[0]->getShipping()
            ? $order->getExtensionAttributes()->getShippingAssignments()[0]->getShipping()->getAddress()
            : null;
    }
}
