<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Vertex\Data\LineItemInterface;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Tax\Model\ModuleManager;

/**
 * Processes Giftwrapping and printed cards on a Creditmemo and converts them to a LineItemInterface
 */
class CreditmemoGiftwrapProcessor implements CreditmemoProcessorInterface
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
    public function process(RequestInterface $request, CreditmemoInterface $creditmemo)
    {
        $lineItems = $request->getLineItems();
        $extensionAttributes = $creditmemo->getExtensionAttributes();

        if ($extensionAttributes === null || !$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            return $request;
        }

        // Order-level Gift Wrapping
        $orderGiftWrap = $this->giftWrapProcessor->processOrderGiftWrap(
            $extensionAttributes->getGwBasePrice(),
            $creditmemo->getStoreId()
        );
        if ($orderGiftWrap) {
            $orderGiftWrap->setUnitPrice($orderGiftWrap->getUnitPrice() * -1);
            $orderGiftWrap->setExtendedPrice($orderGiftWrap->getExtendedPrice() * -1);
            $lineItems[] = $orderGiftWrap;
        }

        // Order-level Printed Card
        $orderGiftcard = $this->giftWrapProcessor->processOrderGiftCard(
            $extensionAttributes->getGwCardBasePrice(),
            $creditmemo->getStoreId()
        );
        if ($orderGiftcard) {
            $orderGiftcard->setUnitPrice($orderGiftcard->getUnitPrice() * -1);
            $orderGiftcard->setExtendedPrice($orderGiftcard->getExtendedPrice() * -1);
            $lineItems[] = $orderGiftcard;
        }

        // Item-level Gift Wrapping
        $lineItems = array_merge($lineItems, $this->processItems($creditmemo));

        $request->setLineItems($lineItems);
        return $request;
    }

    /**
     * Create LineItems for each individual item-level gift wrap
     *
     * @param CreditmemoInterface $creditmemo
     * @return LineItemInterface[]
     */
    private function processItems(CreditmemoInterface $creditmemo)
    {
        /** @var LineItemInterface[] $lineItems */
        $lineItems = [];

        if ($creditmemo->getExtensionAttributes() === null) {
            return $lineItems;
        }

        /** @var CreditmemoItemInterface[] $creditmemoItems Indexed by Order Item ID */
        $creditmemoItems = [];

        /** @var int[] $orderItemIds */
        $orderItemIds = [];

        foreach ($creditmemo->getItems() as $item) {
            if ($item->getQty() < 1) {
                continue;
            }
            $orderItemIds[] = $item->getOrderItemId();
            $creditmemoItems[$item->getOrderItemId()] = $item;
        }

        /** @var float[] $giftWrapAmounts Indexed by Order Item ID */
        $giftWrapAmounts = $this->giftWrapProcessor->getGiftWrapAmounts(
            $creditmemo->getOrderId(),
            $orderItemIds,
            (float)$creditmemo->getExtensionAttributes()->getGwItemsBasePrice()
        );

        foreach ($giftWrapAmounts as $orderItemId => $giftWrapAmount) {
            $lineItems[] = $this->giftWrapProcessor->buildItem(
                -1 * $giftWrapAmount,
                $creditmemoItems[$orderItemId]->getSku(),
                $creditmemo->getStoreId()
            );
        }

        return $lineItems;
    }
}
