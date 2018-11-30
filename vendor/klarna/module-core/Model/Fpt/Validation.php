<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\Fpt;

use Magento\Bundle\Model\Product\Price as ProductPrice;
use Magento\Catalog\Model\Product\Type as ProductBundle;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\AbstractModel;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditMemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;

/**
 * Class Validation
 *
 * @package Klarna\Core\Model\Fpt
 */
class Validation
{

    /**
     * Checking if we have a valid order fpt order item
     *
     * @param InvoiceItem|CreditMemoItem $item
     * @param AbstractModel|Quote        $object
     * @return bool
     */
    public function isValidOrderItem($item, $object)
    {
        $orderItem = $item->getOrderItem();
        $parentItem = $orderItem->getParentItem() ? $object->getItemById($orderItem->getParentItemId()) : null;

        if (!$this->isValidParentOrderItem($parentItem)) {
            return false;
        }

        // Skip if a bundled product with price type dynamic
        if ($orderItem->getProductType() == ProductBundle::TYPE_BUNDLE &&
            $orderItem->getProduct()->getPriceType() == ProductPrice::PRICE_TYPE_DYNAMIC
        ) {
            return false;
        }

        // Skip if parent is a bundle product having price type dynamic
        if (null !== $parentItem && $orderItem->getProductType() == ProductBundle::TYPE_BUNDLE &&
            $orderItem->getProduct()->getPriceType() == ProductPrice::PRICE_TYPE_DYNAMIC
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check if we have a valid parent order item
     *
     * @param OrderItemInterface|null $parentItem
     * @return bool
     */
    private function isValidParentOrderItem($parentItem)
    {
        // Skip if child product of a non bundle parent
        if (!empty($parentItem)
            && $parentItem->getProductType() != ProductBundle::TYPE_BUNDLE
        ) {
            return false;
        }

        // Skip if child product of a bundle parent and bundle product price type is fixed
        if (!empty($parentItem) && $parentItem->getProductType() == ProductBundle::TYPE_BUNDLE &&
            $parentItem->getProduct()->getPriceType() == ProductPrice::PRICE_TYPE_FIXED
        ) {
            return false;
        }

        return true;
    }

    /**
     * Checking if we have a valid fpt quote item
     *
     * @param QuoteItem           $item
     * @param AbstractModel|Quote $object
     * @return bool
     */
    public function isValidQuoteItem($item, $object)
    {
        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            // Skip if bundle product with a dynamic price type
            if ($item->getProductType() == ProductBundle::TYPE_BUNDLE &&
                $item->getProduct()->getPriceType() == ProductPrice::PRICE_TYPE_DYNAMIC
            ) {
                return false;
            }

            // Get quantity multiplier for bundle products
            if (null !== $item->getParentItemId() && ($parentItem = $object->getItemById($item->getParentItemId()))) {
                // Skip if non bundle product or if bundled product with a fixed price type
                if ($parentItem->getProductType() != ProductBundle::TYPE_BUNDLE
                    || $parentItem->getProduct()->getPriceType() == ProductPrice::PRICE_TYPE_FIXED
                ) {
                    return false;
                }
            }
        }

        return true;
    }
}
