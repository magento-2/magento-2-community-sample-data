<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\OrderInterface;
use Vertex\Services\Invoice\RequestInterface;

/**
 * Processes a Magento Order and returns a Vertex Invoice
 *
 * @api
 * @since 2.2.1
 */
interface OrderProcessorInterface
{
    /**
     * Process an Order and return a Vertex Invoice Request
     *
     * @param RequestInterface $request
     * @param OrderInterface $order
     * @return RequestInterface
     */
    public function process(RequestInterface $request, OrderInterface $order);
}
