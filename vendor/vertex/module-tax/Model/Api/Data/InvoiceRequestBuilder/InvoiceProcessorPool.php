<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use Vertex\Services\Invoice\RequestInterface;

/**
 * Contains a pool of InvoiceProcessors for use with the InvoiceRequestBuilder
 *
 * @api
 * @since 2.2.1
 */
class InvoiceProcessorPool implements InvoiceProcessorInterface
{
    /** @var InvoiceProcessorInterface[] */
    private $processors;

    /**
     * @param InvoiceProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        array_walk(
            $processors,
            function ($processor) {
                if (!$processor instanceof InvoiceProcessorInterface) {
                    throw new \InvalidArgumentException(
                        'All processors must be an instance of ' . InvoiceProcessorInterface::class
                    );
                }
            }
        );

        $this->processors = $processors;
    }

    /**
     * Retrieve all LineItemProcessors
     *
     * @return InvoiceProcessorInterface[]
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Use a pool of InvoiceItemProcessors
     *
     * @param RequestInterface $request
     * @param InvoiceInterface $invoice
     * @return RequestInterface
     */
    public function process(RequestInterface $request, InvoiceInterface $invoice)
    {
        foreach ($this->getProcessors() as $processor) {
            $request = $processor->process($request, $invoice);
        }
        return $request;
    }
}
