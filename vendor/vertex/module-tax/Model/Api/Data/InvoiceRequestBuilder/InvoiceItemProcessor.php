<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Vertex\Data\LineItemInterface;
use Vertex\Data\LineItemInterfaceFactory;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Processes Items on an Invoice and converts them to an array of LineItemInterface
 */
class InvoiceItemProcessor implements InvoiceProcessorInterface
{
    /** @var ItemProcessor */
    private $itemProcessor;

    /** @var LineItemInterfaceFactory */
    private $lineItemFactory;

    /** @var TaxClassNameRepository */
    private $taxClassNameRepository;

    /**
     * @param ItemProcessor $itemProcessor
     * @param LineItemInterfaceFactory $lineItemFactory
     * @param TaxClassNameRepository $taxClassNameRepository
     */
    public function __construct(
        ItemProcessor $itemProcessor,
        LineItemInterfaceFactory $lineItemFactory,
        TaxClassNameRepository $taxClassNameRepository
    ) {
        $this->itemProcessor = $itemProcessor;
        $this->lineItemFactory = $lineItemFactory;
        $this->taxClassNameRepository = $taxClassNameRepository;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, InvoiceInterface $invoice)
    {
        /** @var InvoiceItemInterface[] $invoiceItems All InvoiceItems indexed by id */
        $invoiceItems = [];

        /** @var int[] $productIds All Product IDs in the invoice */
        $productSku = [];

        /** @var LineItemInterface[] $lineItems Vertex SDK LineItems to be returned */
        $lineItems = [];

        // Build the invoiceItems, parentItemIds, and productIds arrays

        foreach ($invoice->getItems() as $item) {
            if ($item->getBaseRowTotal() === null) {
                continue;
            }
            $invoiceItems[$item->getOrderItemId()] = $item;
            $productSku[] = $item->getSku();
        }

        $products = $this->itemProcessor->getProductsIndexedBySku($productSku);

        /** @var int[] $taxClasses Key is InvoiceItem ID, Value is Tax Class ID */
        $taxClasses = [];

        foreach ($invoiceItems as $item) {
            $product = $products[$item->getSku()];
            $taxClassAttribute = $product->getCustomAttribute('tax_class_id');
            $taxClassId = $taxClassAttribute ? $taxClassAttribute->getValue() : 0;

            if ($item->getBaseRowTotal() === null) {
                // For bundle products, the parent has a row total of NULL
                continue;
            }

            /** @var LineItemInterface $lineItem */
            $lineItem = $this->lineItemFactory->create();
            $lineItem->setProductCode($item->getSku());
            $lineItem->setQuantity($item->getQty());
            $lineItem->setUnitPrice($item->getBasePrice());
            $lineItem->setExtendedPrice($item->getBaseRowTotal() - $item->getBaseDiscountAmount());
            $lineItem->setLineItemId($item->getOrderItemId());

            $taxClasses[$item->getOrderItemId()] = $taxClassId;

            if ($lineItem->getExtendedPrice() == 0) {
                continue;
            }

            $lineItems[] = $lineItem;
        }

        /** @var string[int] $taxClassNames Tax Classes indexed by ID */
        $taxClassNames = $this->taxClassNameRepository->getListByIds(array_values($taxClasses));

        foreach ($lineItems as $lineItem) {
            $lineItemId = $lineItem->getLineItemId();
            $taxClass = $taxClasses[$lineItemId];
            $taxClassName = $taxClassNames[$taxClass];
            $lineItem->setProductClass($taxClassName);
        }

        $request->setLineItems(array_merge($request->getLineItems(), $lineItems));
        return $request;
    }
}
