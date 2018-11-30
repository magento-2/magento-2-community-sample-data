<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Vertex\Services\Invoice\RequestInterface;

/**
 * Processes Shipping on an Invoice and adds it to a Vertex Invoice's LineItems
 */
class InvoiceShippingProcessor implements InvoiceProcessorInterface
{
    /** @var ShippingProcessor */
    private $shippingProcessor;

    /**
     * @param ShippingProcessor $shippingProcessor
     */
    public function __construct(ShippingProcessor $shippingProcessor)
    {
        $this->shippingProcessor = $shippingProcessor;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, InvoiceInterface $invoice)
    {
        if (!$invoice->getBaseShippingAmount()) {
            return $request;
        }

        $request->setLineItems(
            array_merge(
                $request->getLineItems(),
                $this->shippingProcessor->getShippingLineItems(
                    $invoice->getOrderId(),
                    $invoice->getBaseShippingAmount() - $this->getBaseShippingDiscountAmount($invoice)
                )
            )
        );
        return $request;
    }

    /**
     * Retrieve total discount less line item discounts
     *
     * At the time of this writing, Magento only applies discounts to line items
     * and to the shipping.  Unfortunately for invoices and creditmemos it does
     * not record the amount of shipping being discounted at the time of invoice
     * or credit.  As such, to attempt to figure out what that is, we have to
     * remove the line item discounts from the total discount.
     *
     * This code will break if Magento ever allows discounts to apply to other
     * items like Giftwrapping and Printed Cards
     *
     * @param InvoiceInterface $invoice
     * @return float
     */
    private function getBaseShippingDiscountAmount(InvoiceInterface $invoice)
    {
        $totalDiscount = $invoice->getBaseDiscountAmount() * -1; // discount is stored as a negative here

        $lineItemDiscount = 0;
        foreach ($invoice->getItems() as $item) {
            $lineItemDiscount += (float)$item->getDiscountAmount();
        }

        return $totalDiscount - $lineItemDiscount;
    }
}
