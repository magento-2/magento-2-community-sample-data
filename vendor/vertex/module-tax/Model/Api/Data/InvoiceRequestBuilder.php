<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder\CreditmemoProcessor;
use Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder\InvoiceProcessor;
use Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder\OrderProcessor;

/**
 * Builds an Invoice Request for the Vertex SDK
 *
 * Because there are three types of Magento Entities we need to conceivably
 * build invoices for this class works almost like a proxy.  None of the actual
 * building takes place in it, but instead in it's own processors specifically
 * designed for the different types of Magento Entities.
 *
 * @see OrderProcessor
 * @see InvoiceProcessor
 * @see CreditmemoProcessor
 */
class InvoiceRequestBuilder
{
    /** @var CreditmemoProcessor */
    private $creditmemoProcessor;

    /** @var InvoiceProcessor */
    private $invoiceProcessor;

    /** @var OrderProcessor */
    private $orderProcessor;

    /**
     * @param InvoiceProcessor $invoiceProcessor
     * @param OrderProcessor $orderProcessor
     * @param CreditmemoProcessor $creditmemoProcessor
     */
    public function __construct(
        InvoiceProcessor $invoiceProcessor,
        OrderProcessor $orderProcessor,
        CreditmemoProcessor $creditmemoProcessor
    ) {
        $this->invoiceProcessor = $invoiceProcessor;
        $this->orderProcessor = $orderProcessor;
        $this->creditmemoProcessor = $creditmemoProcessor;
    }

    /**
     * Create an Invoice Request by providing a Magento Invoice
     *
     * @see InvoiceProcessor::process()
     * @param InvoiceInterface $invoice
     * @return RequestInterface
     */
    public function buildFromInvoice(InvoiceInterface $invoice)
    {
        return $this->invoiceProcessor->process($invoice);
    }

    /**
     * Create an Invoice Request by providing a Magento Order
     *
     * @see OrderProcessor::process()
     * @param OrderInterface $order
     * @return RequestInterface
     */
    public function buildFromOrder(OrderInterface $order)
    {
        return $this->orderProcessor->process($order);
    }

    /**
     * Create an Invoice Request by providing a Magento Creditmemo
     *
     * @see CreditmemoProcessor::process()
     * @param CreditmemoInterface $creditmemo
     * @return RequestInterface
     */
    public function buildFromCreditmemo(CreditmemoInterface $creditmemo)
    {
        return $this->creditmemoProcessor->process($creditmemo);
    }
}
