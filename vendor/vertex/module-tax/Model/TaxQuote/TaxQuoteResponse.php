<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxQuote;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;

/**
 * Quotation Response object
 */
class TaxQuoteResponse
{
    /** @var DataObjectFactory */
    private $dataObjectFactory;

    /** @var DataObject[] */
    private $quoteTaxedItems;

    /**
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(DataObjectFactory $dataObjectFactory)
    {
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Parse a Quotation Request response and store it's data here
     *
     * @param array $responseObject
     * @return $this
     */
    public function parseResponse(array $responseObject)
    {
        $taxLineItems = isset($responseObject['LineItem']) ? $responseObject['LineItem'] : [];
        $this->prepareQuoteTaxedItems($taxLineItems);
        return $this;
    }

    /**
     * Prepare the taxable item data
     *
     * @param array $itemsTax
     */
    public function prepareQuoteTaxedItems(array $itemsTax)
    {
        $quoteTaxedItems = [];

        foreach ($itemsTax as $item) {
            $itemTotalTax = 0;
            if (isset($item['TotalTax'])) {
                if (is_array($item['TotalTax'])) {
                    $itemTotalTax += $item['TotalTax']['_'];
                } else {
                    $itemTotalTax += $item['TotalTax'];
                }
            }
            $taxRate = $this->getTaxRate($item);

            $taxPercent = $taxRate * 100;

            if (!isset($item['lineItemId'])) {
                continue;
            }

            $quoteItemId = $item['lineItemId'];
            $taxItemInfo = $this->dataObjectFactory->create();
            $taxItemInfo->setProductClass($this->getProductClass($item));
            $taxItemInfo->setProductSku($this->getProductSku($item));
            if (isset($item['Quantity'])) {
                $taxItemInfo->setProductQty($this->getProductQty($item));
            }
            if (isset($item['UnitPrice'])) {
                $taxItemInfo->setUnitPrice($this->getUnitPrice($item));
            }
            $taxItemInfo->setTaxRate($taxRate);
            $taxItemInfo->setTaxPercent($taxPercent);
            $taxItemInfo->setTaxAmount($itemTotalTax);
            $quoteTaxedItems[$quoteItemId] = $taxItemInfo;
        }
        $this->quoteTaxedItems = $quoteTaxedItems;
    }

    /**
     * Get all items with tax data
     *
     * @return DataObject[]
     */
    public function getQuoteTaxedItems()
    {
        return $this->quoteTaxedItems;
    }

    /**
     * Get the Tax Rate from the response
     *
     * @param array[] $item
     * @return float|int
     */
    private function getTaxRate($item)
    {
        $taxRate = 0;
        if (!isset($item['Taxes'])) {
            return $taxRate;
        }

        foreach ($item['Taxes'] as $key => $taxValue) {
            if (isset($taxValue['EffectiveRate'])) {
                $taxRate += (float)$taxValue['EffectiveRate'];
            } elseif ($key === 'EffectiveRate') {
                $taxRate += (float)$taxValue;
            }
        }

        return $taxRate;
    }

    /**
     * Get an item's product class
     *
     * @param array $item
     * @return string
     */
    private function getProductClass($item)
    {
        return isset($item['Product']['productClass']) ? $item['Product']['productClass'] : '';
    }

    /**
     * Get an item's product sku
     *
     * @param array $item
     * @return string
     */
    private function getProductSku($item)
    {
        return isset($item['Product']['_']) ? $item['Product']['_'] : '';
    }

    /**
     * Get an item's product qty
     *
     * @param array $item
     * @return int
     */
    private function getProductQty($item)
    {
        return isset($item['Quantity']['_']) ? $item['Quantity']['_'] : 0;
    }

    /**
     * Get an item's unit price
     *
     * @param array $item
     * @return int
     */
    private function getUnitPrice($item)
    {
        return isset($item['UnitPrice']['_']) ? (float) $item['UnitPrice']['_'] : 0;
    }
}
