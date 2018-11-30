<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\OrderItemExtensionInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Vertex\Data\LineItemInterface;
use Vertex\Data\LineItemInterfaceFactory;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Processes GiftWrapping information for Invoices and Creditmemos
 */
class GiftWrapProcessor
{
    /** @var TaxClassNameRepository */
    private $classNameRepository;

    /** @var Config */
    private $config;

    /** @var LineItemInterfaceFactory */
    private $lineItemFactory;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param Config $config
     * @param LineItemInterfaceFactory $lineItemFactory
     * @param TaxClassNameRepository $classNameRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Config $config,
        LineItemInterfaceFactory $lineItemFactory,
        TaxClassNameRepository $classNameRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->config = $config;
        $this->lineItemFactory = $lineItemFactory;
        $this->classNameRepository = $classNameRepository;
    }

    /**
     * Determine the items and amount (per item) being invoiced or credited for Giftwrapping
     *
     * Magento stores the item-level Giftwrapping on the Order Item, but does not do so
     * on the Invoice or Creditmemo item.  Instead, the invoice or creditmemo contains the
     * total amount invoiced for items and somehow it is applied to the Order Item's amount
     * invoiced or credited.
     *
     * Since we use unique product codes for the item/giftwrap combination, we go through
     * this process to determine both the items we're invoicing giftwrap for, and the
     * amount of the total invoice for that item.
     *
     * That doesn't explain why we're using percentages though - and perhaps we don't need
     * to be.  Percentages here fix a theoretical scenario where the $totalItemAmount
     * does not equal the sum of the item-level gift wrappings.
     *
     * @param int $orderId Order ID the invoice or creditmemo is for
     * @param int[] $includedOrderItemIds Order Item IDs of items on the invoice or creditmemo
     * @param float $totalItemAmount Total amount invoiced or credited for item-level Gift wrap
     * @return float[] array of amounts indexed by Order Item ID
     */
    public function getGiftWrapAmounts($orderId, $includedOrderItemIds, $totalItemAmount)
    {
        $order = $this->orderRepository->get($orderId);

        $totalAmount = 0;
        $orderItemAmounts = [];

        foreach ($order->getItems() as $orderItem) {
            if (!in_array($orderItem->getItemId(), $includedOrderItemIds, false)) {
                continue;
            }

            if ($orderItem->getExtensionAttributes() !== null &&
                !$orderItem->getExtensionAttributes() instanceof OrderItemExtensionInterface) {
                continue;
            }

            $amount = (float)$orderItem->getExtensionAttributes()->getGwBasePrice();
            $totalAmount += $amount;
            $orderItemAmounts[$orderItem->getItemId()] = $amount;
        }

        if ($totalAmount == 0) {
            return [];
        }

        $resultAmounts = [];

        foreach ($orderItemAmounts as $orderItemId => $orderItemAmount) {
            $percentage = $orderItemAmount / $totalAmount;
            $resultAmount = $totalItemAmount * $percentage;

            if ($resultAmount == 0) {
                continue;
            }

            $resultAmounts[$orderItemId] = $resultAmount;
        }

        return $resultAmounts;
    }

    /**
     * Create a {@see LineItemInterface}
     *
     * @param float $giftWrapAmount
     * @param string $sku
     * @param string|null $scopeCode
     * @return LineItemInterface
     */
    public function buildItem($giftWrapAmount, $sku, $scopeCode = null)
    {
        $productClass = $this->classNameRepository->getById($this->config->getGiftWrappingItemClass($scopeCode));

        /** @var LineItemInterface $lineItem */
        $lineItem = $this->lineItemFactory->create();
        $lineItem->setProductCode($this->config->getGiftWrappingItemCodePrefix($scopeCode) . $sku);
        $lineItem->setProductClass($productClass);
        $lineItem->setQuantity(1);
        $lineItem->setUnitPrice($giftWrapAmount);
        $lineItem->setExtendedPrice($giftWrapAmount);

        return $lineItem;
    }

    /**
     * Create a {@see LineItemInterface} for an Order-level Printed Card
     *
     * @param float|null $basePrice The result of the basePrice getter from the extension attributes
     * @param string|null $scopeCode
     * @return LineItemInterface|null
     */
    public function processOrderGiftCard($basePrice = null, $scopeCode = null)
    {
        if ($basePrice === null || (float)$basePrice <= 0) {
            return null;
        }

        $productCode = $this->config->getPrintedGiftcardCode($scopeCode);
        $productClass = $this->classNameRepository->getById($this->config->getPrintedGiftcardClass($scopeCode));

        /** @var LineItemInterface $lineItem */
        $lineItem = $this->lineItemFactory->create();
        $lineItem->setProductCode($productCode);
        $lineItem->setProductClass($productClass);
        $lineItem->setQuantity(1);
        $lineItem->setUnitPrice((float)$basePrice);
        $lineItem->setExtendedPrice((float)$basePrice);

        return $lineItem;
    }

    /**
     * Create a {@see LineItemInterface} for an Order-level Gift Wrap
     *
     * @param float|null $basePrice The result of the basePrice getter from the extension attributes
     * @param string|null $scopeCode
     * @return LineItemInterface|null
     */
    public function processOrderGiftWrap($basePrice = null, $scopeCode = null)
    {
        if ($basePrice === null || (float)$basePrice <= 0) {
            return null;
        }

        $productCode = $this->config->getGiftWrappingOrderCode($scopeCode);
        $productClass = $this->classNameRepository->getById($this->config->getGiftWrappingOrderClass($scopeCode));

        /** @var LineItemInterface $lineItem */
        $lineItem = $this->lineItemFactory->create();
        $lineItem->setProductCode($productCode);
        $lineItem->setProductClass($productClass);
        $lineItem->setQuantity(1);
        $lineItem->setUnitPrice((float)$basePrice);
        $lineItem->setExtendedPrice((float)$basePrice);

        return $lineItem;
    }
}
