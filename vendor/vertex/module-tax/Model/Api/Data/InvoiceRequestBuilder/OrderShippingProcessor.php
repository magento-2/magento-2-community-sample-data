<?php

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterface;
use Magento\Sales\Api\Data\ShippingInterface;
use Magento\Sales\Api\Data\TotalInterface;
use Vertex\Data\LineItemInterfaceFactory;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Processes Shipping on an Order and adds it to an Invoice Request's LineItems
 */
class OrderShippingProcessor implements OrderProcessorInterface
{
    /** @var TaxClassNameRepository */
    private $classNameRepository;

    /** @var Config */
    private $config;

    /** @var LineItemInterfaceFactory */
    private $lineItemFactory;

    /**
     * @param Config $config
     * @param TaxClassNameRepository $classNameRepository
     * @param LineItemInterfaceFactory $lineItemFactory
     */
    public function __construct(
        Config $config,
        TaxClassNameRepository $classNameRepository,
        LineItemInterfaceFactory $lineItemFactory
    ) {
        $this->config = $config;
        $this->classNameRepository = $classNameRepository;
        $this->lineItemFactory = $lineItemFactory;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, OrderInterface $order)
    {
        $lineItems = $request->getLineItems();

        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null || !$extensionAttributes instanceof OrderExtensionInterface) {
            return $request;
        }

        /** @var ShippingAssignmentInterface[]|null $shippingAssignments */
        $shippingAssignments = $extensionAttributes->getShippingAssignments();

        if ($shippingAssignments === null) {
            return $request;
        }

        // Pre-fetch the shipping tax class since all shipment types have the same one
        $taxClassId = $this->config->getShippingTaxClassId($order->getStoreId());
        $productClass = $this->classNameRepository->getById($taxClassId);

        foreach ($shippingAssignments as $shippingAssignment) {
            // This just gathers those variables
            $shipping = $shippingAssignment->getShipping();
            if ($shipping === null || !$shipping instanceof ShippingInterface) {
                continue;
            }

            $total = $shipping->getTotal();
            if ($total === null || !$total instanceof TotalInterface) {
                continue;
            }

            $cost = $total->getBaseShippingAmount() - $total->getBaseShippingDiscountAmount();

            $lineItem = $this->lineItemFactory->create();
            $lineItem->setProductCode($shipping->getMethod());
            $lineItem->setProductClass($productClass);
            $lineItem->setUnitPrice($cost);
            $lineItem->setQuantity(1);
            $lineItem->setExtendedPrice($cost);

            $lineItems[] = $lineItem;
        }

        $request->setLineItems($lineItems);
        return $request;
    }
}
