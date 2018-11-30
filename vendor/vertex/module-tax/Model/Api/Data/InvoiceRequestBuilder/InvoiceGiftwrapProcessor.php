<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Vertex\Data\LineItemInterface;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Tax\Model\ModuleManager;

/**
 * Processes Giftwrapping and printed cards on an Invoice and converts them to a LineItemInterface
 */
class InvoiceGiftwrapProcessor implements InvoiceProcessorInterface
{
    /** @var GiftWrapProcessor */
    private $giftWrapProcessor;

    /** @var ModuleManager */
    private $moduleManager;

    /**
     * @param ModuleManager $moduleManager
     * @param GiftWrapProcessor $giftWrapProcessor
     */
    public function __construct(
        ModuleManager $moduleManager,
        GiftWrapProcessor $giftWrapProcessor
    ) {
        $this->moduleManager = $moduleManager;
        $this->giftWrapProcessor = $giftWrapProcessor;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, InvoiceInterface $invoice)
    {
        $lineItems = $request->getLineItems();
        $extensionAttributes = $invoice->getExtensionAttributes();

        if (!$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            return $request;
        }

        if ($extensionAttributes !== null) {
            // Order-level Gift Wrapping
            $orderGiftWrap = $this->giftWrapProcessor->processOrderGiftWrap(
                $extensionAttributes->getGwBasePrice(),
                $invoice->getStoreId()
            );
            if ($orderGiftWrap) {
                $lineItems[] = $orderGiftWrap;
            }

            // Order-level Printed Card
            $orderGiftcard = $this->giftWrapProcessor->processOrderGiftCard(
                $extensionAttributes->getGwCardBasePrice(),
                $invoice->getStoreId()
            );
            if ($orderGiftcard) {
                $lineItems[] = $orderGiftcard;
            }
        }

        // Item-level Gift Wrapping
        $lineItems = array_merge($lineItems, $this->processItems($invoice));

        $request->setLineItems($lineItems);
        return $request;
    }

    /**
     * Create LineItems for each individual item-level gift wrap
     *
     * @param InvoiceInterface $invoice
     * @return LineItemInterface[]
     */
    private function processItems(InvoiceInterface $invoice)
    {
        /** @var LineItemInterface[] $lineItems */
        $lineItems = [];

        if ($invoice->getExtensionAttributes() === null) {
            return $lineItems;
        }

        /** @var InvoiceItemInterface[] $invoiceItems Indexed by Order Item ID */
        $invoiceItems = [];

        /** @var int[] $orderItemIds */
        $orderItemIds = [];

        foreach ($invoice->getItems() as $item) {
            if ($item->getQty() < 1) {
                continue;
            }
            $orderItemIds[] = $item->getOrderItemId();
            $invoiceItems[$item->getOrderItemId()] = $item;
        }

        /** @var float[] $giftWrapAmounts Indexed by Order Item ID */
        $giftWrapAmounts = $this->giftWrapProcessor->getGiftWrapAmounts(
            $invoice->getOrderId(),
            $orderItemIds,
            (float)$invoice->getExtensionAttributes()->getGwItemsBasePrice()
        );

        foreach ($giftWrapAmounts as $orderItemId => $giftWrapAmount) {
            $lineItems[] = $this->giftWrapProcessor->buildItem(
                $giftWrapAmount,
                $invoiceItems[$orderItemId]->getSku(),
                $invoice->getStoreId()
            );
        }

        return $lineItems;
    }
}
