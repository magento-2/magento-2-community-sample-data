<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Model\Data\InvoiceSent;
use Vertex\Tax\Model\Data\InvoiceSentFactory;
use Vertex\Tax\Model\Repository\InvoiceSentRepository;

/**
 * Registry of Invoice Sent status
 *
 * Chiefly used to reduce database calls
 */
class InvoiceSentRegistry
{
    /** @var InvoiceSentRepository */
    private $repository;

    /** @var InvoiceSentFactory */
    private $invoiceSentFactory;

    /** @var bool[] Indexed by Invoice ID */
    private $isSentCache = [];

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param InvoiceSentRepository $repository
     * @param InvoiceSentFactory $invoiceSentFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        InvoiceSentRepository $repository,
        InvoiceSentFactory $invoiceSentFactory,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->invoiceSentFactory = $invoiceSentFactory;
        $this->logger = $logger;
    }

    /**
     * Determine if an invoice has already been sent to Vertex
     *
     * @param InvoiceInterface $invoice
     * @return bool
     */
    public function hasInvoiceBeenSentToVertex(InvoiceInterface $invoice)
    {
        if ($this->isInvoiceCachedAsSent($invoice)) {
            return true;
        }

        try {
            $invoiceSent = $this->repository->getByInvoiceId($invoice->getEntityId());
        } catch (NoSuchEntityException $exception) {
            return false;
        }

        $result = $invoiceSent->isSent();
        if ($result) {
            $this->cacheInvoiceSent($invoice);
        }
        return $result;
    }

    /**
     * Declare that an invoice has been sent to Vertex
     *
     * @param InvoiceInterface $invoice
     */
    public function setInvoiceHasBeenSentToVertex(InvoiceInterface $invoice)
    {
        if ($this->isInvoiceCachedAsSent($invoice) || !$invoice->getEntityId()) {
            return;
        }

        /** @var InvoiceSent $invoiceSent */
        $invoiceSent = $this->invoiceSentFactory->create();
        $invoiceSent->setInvoiceId($invoice->getEntityId());
        $invoiceSent->setIsSent(true);
        try {
            $this->repository->save($invoiceSent);
        } catch (AlreadyExistsException $exception) {
            // Too many cooks - or requests, as the case may be.  Perfectly acceptable
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }
    }

    /**
     * Check the cache for whether or not an Invoice has been sent to Vertex
     *
     * @param InvoiceInterface $invoice
     * @return bool
     */
    private function isInvoiceCachedAsSent(InvoiceInterface $invoice)
    {
        return $invoice->getEntityId()
            && isset($this->isSentCache[$invoice->getEntityId()])
            && $this->isSentCache[$invoice->getEntityId()];
    }

    /**
     * Add to the cache that an Invoice has been sent to Vertex
     *
     * @param InvoiceInterface $invoice
     */
    private function cacheInvoiceSent(InvoiceInterface $invoice)
    {
        if ($invoice->getEntityId()) {
            $this->isSentCache[$invoice->getEntityId()] = true;
        }
    }
}
