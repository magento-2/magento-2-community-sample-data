<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Vertex\Services\Invoice\RequestInterface;

/**
 * Processes a Magento Creditmemo and returns a Vertex Invoice
 *
 * @api
 * @since 2.2.1
 */
interface CreditmemoProcessorInterface
{
    /**
     * Process a Creditmemo and return a Vertex Invoice Request
     *
     * @param RequestInterface $request
     * @param CreditmemoInterface $creditmemo
     * @return RequestInterface
     */
    public function process(RequestInterface $request, CreditmemoInterface $creditmemo);
}
