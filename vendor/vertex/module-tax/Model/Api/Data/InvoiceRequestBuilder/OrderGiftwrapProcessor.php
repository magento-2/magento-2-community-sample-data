<?php

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Vertex\Data\LineItemInterface;
use Vertex\Data\LineItemInterfaceFactory;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ModuleManager;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Processes Giftwrapping and printed cards on an Order and converts them to a LineItemInterface
 */
class OrderGiftwrapProcessor implements OrderProcessorInterface
{
    /** @var TaxClassNameRepository */
    private $classNameRepository;

    /** @var Config */
    private $config;

    /** @var LineItemInterfaceFactory */
    private $lineItemFactory;

    /** @var ModuleManager */
    private $moduleManager;

    /**
     * @param ModuleManager $moduleManager
     * @param Config $config
     * @param TaxClassNameRepository $classNameRepository
     * @param LineItemInterfaceFactory $lineItemFactory
     */
    public function __construct(
        ModuleManager $moduleManager,
        Config $config,
        TaxClassNameRepository $classNameRepository,
        LineItemInterfaceFactory $lineItemFactory
    ) {
        $this->moduleManager = $moduleManager;
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

        if ($extensionAttributes === null || !$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            return $request;
        }

        $orderGiftWrap = $this->processOrderGiftWrap($extensionAttributes, $order->getStoreId());
        if ($orderGiftWrap) {
            $lineItems[] = $orderGiftWrap;
        }

        $orderGiftcard = $this->processOrderGiftCard($extensionAttributes, $order->getStoreId());
        if ($orderGiftcard) {
            $lineItems[] = $orderGiftcard;
        }

        foreach ($order->getItems() as $orderItem) {
            $orderItemGiftWrap = $this->processItemGiftWrap($orderItem, $order->getStoreId());
            if ($orderItemGiftWrap) {
                $lineItems[] = $orderItemGiftWrap;
            }
        }

        $request->setLineItems($lineItems);
        return $request;
    }

    /**
     * Create a LineItem for giftwrap if present on an Order Item
     *
     * @param OrderItemInterface $item
     * @param string $storeId
     * @return null|LineItemInterface
     */
    private function processItemGiftWrap(OrderItemInterface $item, $storeId)
    {
        $itemExtension = $item->getExtensionAttributes();

        if ($itemExtension === null || (float)$itemExtension->getGwBasePrice() === 0.0) {
            return null;
        }

        $productCode = $this->config->getGiftWrappingItemCodePrefix($storeId) . $item->getSku();
        $productClass = $this->classNameRepository->getById($this->config->getGiftWrappingItemClass($storeId));

        /** @var LineItemInterface $lineItem */
        $lineItem = $this->lineItemFactory->create();
        $lineItem->setProductCode($productCode);
        $lineItem->setProductClass($productClass);
        $lineItem->setQuantity(1);
        $lineItem->setUnitPrice((float)$itemExtension->getGwBasePrice());
        $lineItem->setExtendedPrice((float)$itemExtension->getGwBasePrice());

        return $lineItem;
    }

    /**
     * Create a LineItem for a printed giftcard if present on an Order
     *
     * @param OrderExtensionInterface $orderExtension
     * @param string $storeId
     * @return null|LineItemInterface
     */
    private function processOrderGiftCard(OrderExtensionInterface $orderExtension, $storeId)
    {
        if ((float)$orderExtension->getGwCardBasePrice() === 0.0) {
            return null;
        }

        $productCode = $this->config->getPrintedGiftcardCode($storeId);
        $productClass = $this->classNameRepository->getById($this->config->getPrintedGiftcardClass($storeId));

        /** @var LineItemInterface $lineItem */
        $lineItem = $this->lineItemFactory->create();
        $lineItem->setProductCode($productCode);
        $lineItem->setProductClass($productClass);
        $lineItem->setQuantity(1);
        $lineItem->setUnitPrice((float)$orderExtension->getGwCardBasePrice());
        $lineItem->setExtendedPrice((float)$orderExtension->getGwCardBasePrice());

        return $lineItem;
    }

    /**
     * Create a LineItem for giftwrap if present on an Order
     *
     * @param OrderExtensionInterface $orderExtension
     * @param string $storeId
     * @return null|LineItemInterface
     */
    private function processOrderGiftWrap(OrderExtensionInterface $orderExtension, $storeId)
    {
        if ((float)$orderExtension->getGwBasePrice() === 0.0) {
            return null;
        }

        $productCode = $this->config->getGiftWrappingOrderCode($storeId);
        $productClass = $this->classNameRepository->getById($this->config->getGiftWrappingOrderClass($storeId));

        /** @var LineItemInterface $lineItem */
        $lineItem = $this->lineItemFactory->create();
        $lineItem->setProductCode($productCode);
        $lineItem->setProductClass($productClass);
        $lineItem->setQuantity(1);
        $lineItem->setUnitPrice((float)$orderExtension->getGwBasePrice());
        $lineItem->setExtendedPrice((float)$orderExtension->getGwBasePrice());

        return $lineItem;
    }
}
