<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use Vertex\Services\Invoice\RequestInterface;

/**
 * Processes a Magento Invoice and returns a Vertex Invoice
 *
 * @api
 * @since 2.2.1
 */
interface InvoiceProcessorInterface
{
    /**
     * Process an Invoice and returns a Vertex Invoice Request
     *
     * @param RequestInterface $request
     * @param InvoiceInterface $invoice
     * @return RequestInterface
     */
    public function process(RequestInterface $request, InvoiceInterface $invoice);
}
