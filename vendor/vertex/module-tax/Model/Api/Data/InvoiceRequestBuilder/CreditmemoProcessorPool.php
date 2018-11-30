<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Vertex\Services\Invoice\RequestInterface;

/**
 * Contains a pool of CreditmemoProcessors for use with the InvoiceRequestBuilder
 *
 * @api
 * @since 2.2.1
 */
class CreditmemoProcessorPool implements CreditmemoProcessorInterface
{
    /** @var CreditmemoProcessorInterface[] */
    private $processors;

    /**
     * @param CreditmemoProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        array_walk(
            $processors,
            function ($processor) {
                if (!$processor instanceof CreditmemoProcessorInterface) {
                    throw new \InvalidArgumentException(
                        'All processors must be an instance of ' . CreditmemoProcessorInterface::class
                    );
                }
            }
        );

        $this->processors = $processors;
    }

    /**
     * Retrieve all LineItemProcessors
     *
     * @return CreditmemoProcessorInterface[]
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Use a pool of CreditmemoItemProcessors
     *
     * @param RequestInterface $request
     * @param CreditmemoInterface $creditmemo
     * @return RequestInterface
     */
    public function process(RequestInterface $request, CreditmemoInterface $creditmemo)
    {
        foreach ($this->getProcessors() as $processor) {
            $request = $processor->process($request, $creditmemo);
        }
        return $request;
    }
}
