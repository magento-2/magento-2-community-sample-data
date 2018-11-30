<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\Checkout\Orderline;

use Klarna\Core\Api\BuilderInterface;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\UrlInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditMemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Generate order item line details
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Items extends AbstractLine
{

    /**
     * Checkout item types
     */
    const ITEM_TYPE_PHYSICAL = 'physical';
    const ITEM_TYPE_VIRTUAL  = 'digital';

    /**
     * Order lines is not a total collector, it's a line item collector
     *
     * @var bool
     */
    protected $isTotalCollector = false;

    /**
     * Collect totals process.
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     * @throws \Klarna\Core\Exception
     */
    public function collect(BuilderInterface $checkout)
    {
        $object = $checkout->getObject();
        $items = [];

        foreach ($object->getAllItems() as $item) {
            $qtyMultiplier = 1;

            $item = $this->getItem($item);
            $store = $item->getStore();
            $product = $item->getProduct();

            $parentItem = $item->getParentItem()
                ?: ($item->getParentItemId() ? $object->getItemById($item->getParentItemId()) : null);

            if ($this->shouldSkip($parentItem, $item)) {
                continue;
            }

            if (isset($parentItem)) {
                $product = $parentItem->getProduct();
                $qtyMultiplier = $parentItem->getQty();
            }
            $product->setStoreId($store->getId());

            $items[] = $this->processItem($product, $item, $qtyMultiplier);
        }
        $checkout->setItems($items);

        return $this;
    }

    /**
     * @param QuoteItem|InvoiceItem|CreditMemoItem $item
     * @return QuoteItem|OrderItem
     */
    private function getItem($item)
    {
        if ($item instanceof InvoiceItem || $item instanceof CreditMemoItem) {
            $orderItem =  $item->getOrderItem();
            $orderItem->setCurrentInvoiceRefundItemQty($item->getQty());
            return $orderItem;
        }

        return $item;
    }

    /**
     * @param $parentItem
     * @param $item
     * @return bool
     */
    private function shouldSkip($parentItem, $item)
    {
        // Skip if bundle product with a dynamic price type
        if (Type::TYPE_BUNDLE == $item->getProductType()
            && Price::PRICE_TYPE_DYNAMIC == $item->getProduct()->getPriceType()
        ) {
            return true;
        }

        if (!$parentItem) {
            return false;
        }

        // Skip if child product of a non bundle parent
        if (Type::TYPE_BUNDLE != $parentItem->getProductType()) {
            return true;
        }

        // Skip if non bundle product or if bundled product with a fixed price type
        if (Type::TYPE_BUNDLE != $parentItem->getProductType()
            || Price::PRICE_TYPE_FIXED == $parentItem->getProduct()->getPriceType()
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Product                              $product
     * @param QuoteItem|InvoiceItem|CreditMemoItem $item
     * @param float|int                            $qtyMultiplier
     * @return array
     * @throws \Klarna\Core\Exception
     */
    private function processItem($product, $item, $qtyMultiplier)
    {
        $productUrl = $product->getUrlInStore();
        $imageUrl = $this->getImageUrl($product);

        $_item = [
            'type'          => $item->getIsVirtual() ? self::ITEM_TYPE_VIRTUAL : self::ITEM_TYPE_PHYSICAL,
            'reference'     => substr($item->getSku(), 0, 64),
            'name'          => $item->getName(),
            'quantity'      => ceil($this->getItemQty($item) * $qtyMultiplier),
            'discount_rate' => 0,
            'product_url'   => $productUrl,
            'image_url'     => $imageUrl
        ];

        if ($this->klarnaConfig->isSeparateTaxLine($product->getStore())) {
            $_item['tax_rate'] = 0;
            $_item['total_tax_amount'] = 0;
            $_item['unit_price'] = $this->helper->toApiFloat($item->getBasePrice())
                ?: $this->helper->toApiFloat($item->getBaseOriginalPrice());
            $_item['total_amount'] = $this->helper->toApiFloat($item->getBaseRowTotal());
        } else {
            $taxRate = 0;
            if ($item->getBaseRowTotal() > 0) {
                $taxRate = ($item->getTaxPercent() > 0) ? $item->getTaxPercent()
                    : ($item->getBaseTaxAmount() / $item->getBaseRowTotal() * 100);
            }

            $taxAmount = $this->calculator->calcTaxAmount($item->getBaseRowTotalInclTax(), $taxRate, true);
            $_item['tax_rate'] = $this->helper->toApiFloat($taxRate);
            $_item['total_tax_amount'] = $this->helper->toApiFloat($taxAmount);
            $_item['unit_price'] = $this->helper->toApiFloat($item->getBasePriceInclTax())
                ?: $this->helper->toApiFloat($item->getBaseRowTotalInclTax());
            $_item['total_amount'] = $this->helper->toApiFloat($item->getBaseRowTotalInclTax());
        }

        return $_item;
    }

    /**
     * Get image for product
     *
     * @param Product $product
     * @return string
     */
    private function getImageUrl(Product $product)
    {
        if (!$product->getSmallImage()) {
            return null;
        }
        $baseUrl = $product->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $baseUrl . 'catalog/product' . $product->getSmallImage();
    }

    /**
     * @param QuoteItem|InvoiceItem|CreditMemoItem $item
     * @return int
     */
    private function getItemQty($item)
    {
        $methods = ['getQty', 'getCurrentInvoiceRefundItemQty', 'getQtyOrdered'];
        foreach ($methods as $method) {
            if ($item->$method() !== null) {
                return $item->$method();
            }
        }
        return 0;
    }

    /**
     * Add order details to checkout request
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     */
    public function fetch(BuilderInterface $checkout)
    {
        if ($checkout->getItems()) {
            foreach ($checkout->getItems() as $item) {
                $checkout->addOrderLine($item);
            }
        }

        return $this;
    }
}
