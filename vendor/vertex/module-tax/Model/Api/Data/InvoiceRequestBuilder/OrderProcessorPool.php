<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\OrderInterface;
use Vertex\Services\Invoice\RequestInterface;

/**
 * Contains a pool of OrderProcessors for use with the InvoiceRequestBuilder
 *
 * @api
 * @since 2.2.1
 */
class OrderProcessorPool implements OrderProcessorInterface
{
    /** @var OrderProcessorInterface[] */
    private $processors;

    /**
     * @param OrderProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        array_walk(
            $processors,
            function ($processor) {
                if (!$processor instanceof OrderProcessorInterface) {
                    throw new \InvalidArgumentException(
                        'All processors must be an instance of ' . OrderProcessorInterface::class
                    );
                }
            }
        );

        $this->processors = $processors;
    }

    /**
     * Retrieve all LineItemProcessors
     *
     * @return OrderProcessorInterface[]
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Use a pool of OrderProcessors to update the Invoice Request
     *
     * @param RequestInterface $request
     * @param OrderInterface $order
     * @return RequestInterface
     */
    public function process(RequestInterface $request, OrderInterface $order)
    {
        foreach ($this->getProcessors() as $processor) {
            $request = $processor->process($request, $order);
        }
        return $request;
    }
}
