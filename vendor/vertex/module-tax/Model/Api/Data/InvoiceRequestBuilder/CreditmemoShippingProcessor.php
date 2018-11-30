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

/**
 * Processes Shipping on a Creditmemo and adds it to a Vertex Invoice's LineItems
 */
class CreditmemoShippingProcessor implements CreditmemoProcessorInterface
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
    public function process(RequestInterface $request, CreditmemoInterface $creditmemo)
    {
        if (!$creditmemo->getBaseShippingAmount()) {
            return $request;
        }

        /** @var LineItemInterface[] $lineItems */
        $lineItems = array_map(
            function (LineItemInterface $lineItem) {
                // Since we are creating an Invoice record, make these negative
                $lineItem->setUnitPrice(-1 * $lineItem->getUnitPrice());
                $lineItem->setExtendedPrice(-1 * $lineItem->getExtendedPrice());
                return $lineItem;
            },
            $this->shippingProcessor->getShippingLineItems(
                $creditmemo->getOrderId(),
                $creditmemo->getBaseShippingAmount() - $this->getBaseShippingDiscountAmount($creditmemo)
            )
        );

        $request->setLineItems(array_merge($request->getLineItems(), $lineItems));
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
     * @param CreditmemoInterface $creditmemo
     * @return float
     */
    private function getBaseShippingDiscountAmount(CreditmemoInterface $creditmemo)
    {
        $totalDiscount = $creditmemo->getBaseDiscountAmount() * -1; // discount is stored as a negative here
        $lineItemDiscount = array_reduce(
            $creditmemo->getItems(),
            function ($result, CreditmemoItemInterface $item) {
                return $result + (float)$item->getDiscountAmount();
            },
            0
        );

        return $totalDiscount - $lineItemDiscount;
    }
}
