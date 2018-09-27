<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxInvoice;

use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Sales\Block\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ModuleManager;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Provide formatted Line Item data for an Invoice request
 */
class ItemFormatter
{
    /** @var Config */
    private $config;

    /** @var ModuleManager */
    private $moduleManager;

    /** @var TaxClassNameRepository */
    private $taxClassNameRepository;

    /**
     * @param Config $config
     * @param ModuleManager $moduleManager
     * @param TaxClassNameRepository $taxClassNameRepository
     */
    public function __construct(
        Config $config,
        ModuleManager $moduleManager,
        TaxClassNameRepository $taxClassNameRepository
    ) {
        $this->config = $config;
        $this->moduleManager = $moduleManager;
        $this->taxClassNameRepository = $taxClassNameRepository;
    }

    /**
     * Prepare the data object for an Order Item
     *
     * Always send discounted. Discount on TotalRowAmount
     *
     * @param OrderItem $item
     * @param string $type
     * @param OrderItem|InvoiceItem|CreditmemoItem|null $originalEntityItem
     * @param string|null $event
     * @return array
     */
    public function getPreparedItemData(OrderItem $item, $type = 'ordered', $originalEntityItem = null, $event = null)
    {
        $itemData = [];

        if ($item->getProduct() !== null) {
            $itemData['product_class'] = $this->taxClassNameRepository->getById(
                $item->getProduct()
                    ->getTaxClassId()
            );
        } else {
            $itemData['product_class'] = 'None';
        }

        $itemData['product_code'] = $item->getSku();
        $itemData['item_id'] = $item->getId();

        $store = $item->getStoreId();
        $applyOn = $this->config->getApplyTaxOn($store);

        $price = \intval($applyOn, 10) === Config::VALUE_APPLY_ON_ORIGINAL_ONLY
            ? $item->getBaseOriginalPrice()
            : $item->getBasePrice();

        $itemData['price'] = $price;
        $itemData['qty'] = $this->determineItemQty($item, $originalEntityItem, $type);
        $itemData['extended_price'] = $this->determineExtendedPrice($item, $originalEntityItem, $type);

        if ($event === 'cancel' || $event === 'refund') {
            $itemData['price'] = -1 * $itemData['price'];
            $itemData['extended_price'] = -1 * $itemData['extended_price'];
        }

        return $itemData;
    }

    /**
     * Determine the quantity of an item
     *
     * @param OrderItem $item
     * @param OrderItem|InvoiceItem|CreditmemoItem $originalEntityItem
     * @param string $type
     * @return int
     */
    private function determineItemQty($item, $originalEntityItem, $type)
    {
        if ($type === 'invoiced') {
            return $originalEntityItem->getQty();
        }

        if ($this->config->requestByInvoiceCreation($item->getStoreId())) {
            return $item->getQtyOrdered() - $item->getQtyInvoiced();
        }

        return $item->getQtyOrdered();
    }

    /**
     * Determine the extended price for an item
     *
     * @param OrderItem $item
     * @param OrderItem|InvoiceItem|CreditmemoItem $originalEntityItem
     * @param string $type
     * @return int
     */
    private function determineExtendedPrice($item, $originalEntityItem, $type)
    {
        $useOriginalPrice = $this->config->getApplyTaxOn($item->getStoreId()) == Config::VALUE_APPLY_ON_ORIGINAL_ONLY;

        if ($type === 'invoiced') {
            $qty = $this->determineItemQty($item, $originalEntityItem, $type);
            $rowTotal = $useOriginalPrice
                ? ($item->getBaseOriginalPrice() * $qty)
                : $originalEntityItem->getBaseRowTotal();
            return $rowTotal - $originalEntityItem->getBaseDiscountAmount();
        }

        $byInvoiceCreation = $this->config->requestByInvoiceCreation($item->getStoreId());

        $rowTotal = $useOriginalPrice
            ? $item->getBaseOriginalPrice() * $item->getQtyOrdered()
            : $item->getBaseRowTotal();
        if ($byInvoiceCreation) {
            return $rowTotal
                - $item->getBaseRowInvoiced()
                - $item->getBaseDiscountAmount()
                + $item->getBaseDiscountInvoiced();
        }

        return $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
    }

    /**
     * Create Giftwrapping data
     *
     * @param OrderItem $item
     * @param string $type
     * @param OrderItem|InvoiceItem|CreditmemoItem $originalEntityItem
     * @param string|null $event
     * @return array
     */
    public function getPreparedItemGiftWrap(
        OrderItem $item,
        $type = 'ordered',
        $originalEntityItem = null,
        $event = null
    ) {
        $itemData = [];

        $store = $item->getStoreId();
        $itemData['product_class'] = substr(
            $this->taxClassNameRepository->getById($this->config->getGiftWrappingItemClass($store)),
            0,
            Config::MAX_CHAR_PRODUCT_CODE_ALLOWED
        );
        $itemData['product_code'] = substr(
            $this->config->getGiftWrappingItemCodePrefix($store) . '-' . $item->getSku(),
            0,
            Config::MAX_CHAR_PRODUCT_CODE_ALLOWED
        );

        if ($type === 'invoiced') {
            $price = $item->getGwBasePriceInvoiced();
        } else {
            $price = $item->getGwBasePrice();
        }

        $itemData['price'] = $price;
        if (empty($itemData['price'])) {
            $itemData['price'] = 0;
        }
        $itemData['qty'] = $this->determineItemQty($item, $originalEntityItem, $type);
        $requestByInvoiceCreation = $this->config->requestByInvoiceCreation($store);
        $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];

        if ($type === 'invoiced' || ($type === 'ordered' && $requestByInvoiceCreation)) {
            $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];
        }

        if ($event === 'cancel' || $event === 'refund') {
            $itemData['price'] = -1 * $itemData['price'];
            $itemData['extended_price'] = -1 * $itemData['extended_price'];
        }

        $itemData['lineItemNumber'] = 'gift_wrap_' . $item->getId();

        return $itemData;
    }

    /**
     * Add Order Item data to the $orderItems array
     *
     * @param array $orderItems
     * @param string $typeId
     * @param string $event
     * @param OrderItem|InvoiceItem|CreditmemoItem $originalItem
     * @return void
     */
    public function addPreparedOrderItems(array &$orderItems, $typeId, $event, $originalItem)
    {
        $giftWrappingEnabled = $this->moduleManager->isEnabled('Magento_GiftWrapping');

        $item = $this->getOrderItem($originalItem);
        if ($item === null || $item->getParentItem()) {
            return;
        }

        $hasChildren = $item->getHasChildren();
        $hasChildrenAndProduct = $hasChildren && $item->getProduct() !== null;

        /** @var OrderItem[] $processItems */
        if ($hasChildrenAndProduct && (int)$item->getProduct()->getPriceType() === AbstractType::CALCULATE_CHILD) {
            $processItems = $item->getChildrenItems();
        } else {
            $processItems[] = $item;
        }

        foreach ($processItems as $item) {
            $orderItems[] = $this->getPreparedItemData($item, $typeId, $originalItem, $event);
            if ($giftWrappingEnabled && $item->getGwId()) {
                $orderItems[] = $this->getPreparedItemGiftWrap(
                    $item,
                    $typeId,
                    $originalItem,
                    $event
                );
            }
        }
    }

    /**
     * Retrieve the original Order Item object
     *
     * @param OrderItem|InvoiceItem|Creditmemo $originalItem
     * @return OrderItem|null
     */
    private function getOrderItem($originalItem)
    {
        /** @var OrderItem $item */
        if ($originalItem instanceof InvoiceItem || $originalItem instanceof CreditmemoItem) {
            return $originalItem->getOrderItem();
        }
        if ($originalItem instanceof OrderItem) {
            return $originalItem;
        }
        return null;
    }
}
