<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxQuote;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Vertex\Data\LineItemInterface;
use Vertex\Data\LineItemInterfaceFactory;
use Vertex\Services\Quote\ResponseInterface;

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
     * Get all items with tax data
     *
     * @return DataObject[]
     */
    public function getQuoteTaxedItems()
    {
        return $this->quoteTaxedItems;
    }

    /**
     * Parse a Quotation Request response and store it's data here
     *
     * @param ResponseInterface $response
     * @return $this
     */
    public function parseResponse(ResponseInterface $response)
    {
        $this->prepareQuoteTaxedItems($response->getLineItems());
        return $this;
    }

    /**
     * Prepare the taxable item data
     *
     * @param LineItemInterface[] $itemsTax
     */
    public function prepareQuoteTaxedItems(array $itemsTax)
    {
        $quoteTaxedItems = [];

        foreach ($itemsTax as $item) {
            $itemTotalTax = $item->getTotalTax() ?: 0;

            $taxRate = $this->getTaxRate($item);
            $taxPercent = $taxRate * 100;

            $itemId = $item->getLineItemId();
            if ($itemId === null) {
                continue;
            }

            $taxItemInfo = $this->dataObjectFactory->create();
            $taxItemInfo->setProductClass($this->getProductClass($item));
            $taxItemInfo->setProductSku($this->getProductSku($item));
            if ($item->getQuantity() !== null) {
                $taxItemInfo->setProductQty($item->getQuantity());
            }
            if ($item->getUnitPrice() !== null) {
                $taxItemInfo->setUnitPrice($item->getUnitPrice());
            }
            $taxItemInfo->setTaxRate($taxRate);
            $taxItemInfo->setTaxPercent($taxPercent);
            $taxItemInfo->setTaxAmount($itemTotalTax);
            $quoteTaxedItems[$itemId] = $taxItemInfo;
        }
        $this->quoteTaxedItems = $quoteTaxedItems;
    }

    /**
     * Get an item's product class
     *
     * @param LineItemInterface $item
     * @return string
     */
    private function getProductClass(LineItemInterface $item)
    {
        return $item->getProductClass() ?: '';
    }

    /**
     * Get an item's product sku
     *
     * @param LineItemInterface $item
     * @return string
     */
    private function getProductSku(LineItemInterface $item)
    {
        return $item->getProductCode() ?: '';
    }

    /**
     * Get the Tax Rate from the response
     *
     * @param LineItemInterface $item
     * @return float|int
     */
    private function getTaxRate(LineItemInterface $item)
    {
        $taxRate = 0;

        foreach ($item->getTaxes() as $tax) {
            $taxRate += $tax->getEffectiveRate();
        }

        return $taxRate;
    }
}
