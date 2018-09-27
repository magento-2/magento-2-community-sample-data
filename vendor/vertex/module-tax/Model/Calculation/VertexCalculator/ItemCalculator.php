<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Calculation\VertexCalculator;

use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Model\ItemCode;
use Vertex\Tax\Model\ItemType;
use Vertex\Tax\Model\TaxRegistry;

/**
 * Determines taxes for an Item
 */
class ItemCalculator
{
    /** @var TaxRegistry */
    private $taxRegistry;

    /** @var DataObjectFactory */
    private $objectFactory;

    /** @var GwItemKeyManager */
    private $gwItemKeyManager;

    /** @var ItemType */
    private $itemType;

    /** @var ItemCode */
    private $itemCode;

    /** @var ItemKeyManager */
    private $itemKeyManager;

    /** @var PriceCurrencyInterface */
    private $currency;

    /**
     * @param TaxRegistry $taxRegistry
     * @param DataObjectFactory $objectFactory
     * @param ItemType $itemType
     * @param ItemCode $itemCode
     * @param ItemKeyManager $itemKeyManager
     * @param GwItemKeyManager $gwItemKeyManager
     * @param PriceCurrencyInterface $currency
     */
    public function __construct(
        TaxRegistry $taxRegistry,
        DataObjectFactory $objectFactory,
        ItemType $itemType,
        ItemCode $itemCode,
        ItemKeyManager $itemKeyManager,
        GwItemKeyManager $gwItemKeyManager,
        PriceCurrencyInterface $currency
    ) {
        $this->taxRegistry = $taxRegistry;
        $this->objectFactory = $objectFactory;
        $this->itemType = $itemType;
        $this->itemCode = $itemCode;
        $this->itemKeyManager = $itemKeyManager;
        $this->gwItemKeyManager = $gwItemKeyManager;
        $this->currency = $currency;
    }

    /**
     * Retrieve an object containing the relevant taxes for the QuoteDetailsItem
     *
     * @param QuoteDetailsItemInterface $item
     * @param array $itemTaxes
     * @param bool $round
     * @return DataObject
     */
    public function calculateItemTax(
        QuoteDetailsItemInterface $item,
        array $itemTaxes,
        $round = true
    ) {
        /** @var DataObject $itemTax */
        $itemTax = $this->objectFactory->create();

        $type = $item->getType();

        switch ($type) {
            case $this->itemType->product():
                $this->updateItemTaxFromKey($itemTax, $itemTaxes, $this->itemKeyManager->get($item));
                break;
            case $this->itemType->giftWrap():
                $this->updateItemTaxFromKey($itemTax, $itemTaxes, $this->gwItemKeyManager->get($item));
                break;
            case $this->itemType->shipping():
                $this->updateItemTaxFromKey($itemTax, $itemTaxes, VertexCalculator::VERTEX_SHIPPING_LINE_ITEM_ID);
                break;
            case $this->itemType->orderPrintedCard():
                $this->updateItemTaxFromKey($itemTax, $itemTaxes, $this->itemCode->printedCard());
                break;
            case $this->itemType->orderGiftWrap():
                $this->updateItemTaxFromKey($itemTax, $itemTaxes, $this->itemCode->giftWrap());
                break;
        }

        $this->convertNonBaseCurrency($itemTax, $item, $round);

        return $itemTax;
    }

    /**
     * Given a string to search for, update the itemTax with the calculated taxes.
     *
     * @param DataObject $itemTax
     * @param array $itemTaxes
     * @param string $key
     */
    private function updateItemTaxFromKey(DataObject $itemTax, array $itemTaxes, $key)
    {
        if (isset($itemTaxes[$key])) {
            $itemTax->addData($itemTaxes[$key]->getData());
        }
    }

    /**
     * Re-calculate taxes if a currency is not in the base currency
     *
     * Magento_Tax runs through this function once for each currency.  We've calculated and cached the rate for the base
     * currency.  In this way, if the item's UnitPrice isn't equal to our UnitPrice from Vertex we know it's a different
     * currency and recalculate it.
     *
     * @param DataObject $itemTax
     * @param QuoteDetailsItemInterface $item
     * @param bool $round
     * @return void
     */
    private function convertNonBaseCurrency(DataObject $itemTax, QuoteDetailsItemInterface $item, $round = true)
    {
        if ($itemTax->hasUnitPrice() && $itemTax->getUnitPrice() !== $item->getUnitPrice()) {
            $rowAmount = ($item->getQuantity() * $item->getUnitPrice()) - $item->getDiscountAmount();
            $taxAmount = $rowAmount * $itemTax->getTaxRate();
            $itemTax->setTaxAmount($round ? $this->currency->round($taxAmount) : $taxAmount);
        }
    }

    /**
     * Retrieve Vertex Item Taxes
     *
     * @return mixed
     */
    public function getVertexItemTaxes()
    {
        return $this->taxRegistry->lookupTaxes();
    }

    /**
     * Create an empty item tax object
     *
     * @return DataObject
     */
    public function getEmptyItemTax()
    {
        return $this->objectFactory->create();
    }
}
