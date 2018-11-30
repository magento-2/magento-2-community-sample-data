<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Framework\Intl\DateTimeFactory;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Services\Invoice\RequestInterfaceFactory;
use Vertex\Tax\Model\Api\Data\CustomerBuilder;
use Vertex\Tax\Model\Api\Data\SellerBuilder;
use Vertex\Tax\Model\Api\Utility\DeliveryTerm;
use Vertex\Tax\Model\Config;

/**
 * Processes a Magento Creditmemo and returns a Vertex Invoice Request
 */
class CreditmemoProcessor
{
    /** @var Config */
    private $config;

    /** @var CustomerBuilder */
    private $customerBuilder;

    /** @var DateTimeFactory */
    private $dateTimeFactory;

    /** @var DeliveryTerm */
    private $deliveryTerm;

    /** @var OrderAddressRepositoryInterface */
    private $orderAddressRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CreditmemoProcessorInterface */
    private $processorPool;

    /** @var RequestInterfaceFactory */
    private $requestFactory;

    /** @var SellerBuilder */
    private $sellerBuilder;

    /**
     * @param OrderAddressRepositoryInterface $orderAddressRepository
     * @param SellerBuilder $sellerBuilder
     * @param CustomerBuilder $customerBuilder
     * @param RequestInterfaceFactory $requestFactory
     * @param Config $config
     * @param DeliveryTerm $deliveryTerm
     * @param DateTimeFactory $dateTimeFactory
     * @param CreditmemoProcessorInterface $processorPool
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderAddressRepositoryInterface $orderAddressRepository,
        SellerBuilder $sellerBuilder,
        CustomerBuilder $customerBuilder,
        RequestInterfaceFactory $requestFactory,
        Config $config,
        DeliveryTerm $deliveryTerm,
        DateTimeFactory $dateTimeFactory,
        CreditmemoProcessorInterface $processorPool,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderAddressRepository = $orderAddressRepository;
        $this->sellerBuilder = $sellerBuilder;
        $this->customerBuilder = $customerBuilder;
        $this->requestFactory = $requestFactory;
        $this->config = $config;
        $this->deliveryTerm = $deliveryTerm;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->processorPool = $processorPool;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Process a Magento Creditmemo and return a Vertex Invoice Request
     *
     * @param CreditmemoInterface $creditmemo
     * @return RequestInterface
     */
    public function process(CreditmemoInterface $creditmemo)
    {
        $address = $creditmemo->getShippingAddressId()
            ? $this->orderAddressRepository->get($creditmemo->getShippingAddressId())
            : $this->orderAddressRepository->get($creditmemo->getBillingAddressId());

        $order = $this->orderRepository->get($creditmemo->getOrderId());

        $scopeCode = $creditmemo->getStoreId();

        $seller = $this->sellerBuilder
            ->setScopeType(ScopeInterface::SCOPE_STORE)
            ->setScopeCode($scopeCode)
            ->build();

        $customer = $this->customerBuilder->buildFromOrderAddress(
            $address,
            $address->getCustomerId(),
            $order->getCustomerGroupId(),
            $scopeCode
        );

        /** @var RequestInterface $request */
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

        $request = $this->processorPool->process($request, $creditmemo);

        return $request;
    }
}
