<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Service;

use Magento\Store\Model\ScopeInterface;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Tax\Api\InvoiceInterface;
use Vertex\Tax\Model\Api\Logger;
use Vertex\Tax\Model\Api\Service\InvoiceBuilder;

/**
 * Default implementation of {@see InvoiceInterface}
 */
class InvoiceProxy implements InvoiceInterface
{
    /** @var InvoiceBuilder */
    private $invoiceBuilder;

    /** @var Logger */
    private $logger;

    /**
     * @param Logger $logger
     * @param InvoiceBuilder $invoiceBuilder
     */
    public function __construct(Logger $logger, InvoiceBuilder $invoiceBuilder)
    {
        $this->logger = $logger;
        $this->invoiceBuilder = $invoiceBuilder;
    }

    /**
     * @inheritdoc
     */
    public function record(RequestInterface $request, $scopeCode = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        $invoice = $this->invoiceBuilder
            ->setScopeCode($scopeCode)
            ->setScopeType($scopeType)
            ->build($scopeCode);

        return $this->logger->wrapCall(
            function () use ($invoice, $request) {
                return $invoice->record($request);
            },
            'invoice'
        );
    }
}
