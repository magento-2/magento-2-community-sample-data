<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Vertex\Exception\ApiException;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Tax\Api\InvoiceInterface;

/**
 * Service for sending Tax Invoices to the Vertex API
 */
class TaxInvoice
{
    const REQUEST_TYPE = 'invoice';
    const REQUEST_TYPE_REFUND = 'invoice_refund';

    /** @var InvoiceInterface */
    private $invoice;

    /** @var LoggerInterface */
    private $logger;

    /** @var ManagerInterface */
    private $messageManager;

    /** @var OrderStatusHistoryRepositoryInterface */
    private $orderStatusRepository;

    /**
     * @param LoggerInterface $logger
     * @param ManagerInterface $messageManager
     * @param OrderStatusHistoryRepositoryInterface $orderStatusRepository
     * @param InvoiceInterface $invoice
     */
    public function __construct(
        LoggerInterface $logger,
        ManagerInterface $messageManager,
        OrderStatusHistoryRepositoryInterface $orderStatusRepository,
        InvoiceInterface $invoice
    ) {
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->invoice = $invoice;
    }

    /**
     * Send the Invoice Request to the API
     *
     * @param RequestInterface $request
     * @param Order $order
     * @return bool
     */
    public function sendInvoiceRequest(RequestInterface $request, Order $order)
    {
        try {
            $response = $this->invoice->record($request, $order->getStoreId(), ScopeInterface::SCOPE_STORE);
        } catch (\Exception $e) {
            $this->addErrorMessage(__('Could not submit invoice to Vertex.'), $e);
            return false;
        }

        $totalTax = $response->getTotalTax();

        $comment = $order->addStatusHistoryComment(
            'Vertex Invoice sent successfully. Amount: $' . number_format($totalTax, 2)
        );
        try {
            $this->orderStatusRepository->save($comment);
        } catch (\Exception $originalException) {
            $exception = new \Exception('Could not save Vertex invoice comment', 0, $originalException);
            $this->logger->critical(
                $exception->getMessage() . PHP_EOL . $exception->getTraceAsString()
            );
        }
        return true;
    }

    /**
     * Send the Creditmemo Request to the API
     *
     * @param RequestInterface $request
     * @param Order|null $order
     * @return bool
     */
    public function sendRefundRequest(RequestInterface $request, Order $order)
    {
        try {
            $response = $this->invoice->record($request, $order->getStoreId(), ScopeInterface::SCOPE_STORE);
        } catch (\Exception $e) {
            $this->addErrorMessage(__('Could not submit refund to Vertex.'), $e);
            return false;
        }

        $totalTax = $response->getTotalTax();

        $comment = $order->addStatusHistoryComment(
            'Vertex Invoice refunded successfully. Amount: $' . number_format($totalTax, 2)
        );
        try {
            $this->orderStatusRepository->save($comment);
        } catch (\Exception $originalException) {
            $exception = new CouldNotSaveException(
                __('Could not save Vertex invoice refund comment'),
                $originalException
            );
            $this->logger->critical(
                $exception->getMessage() . PHP_EOL . $exception->getTraceAsString()
            );
        }
        return true;
    }

    private function addErrorMessage(Phrase $friendlyPhrase, $exception = null)
    {
        $friendlyMessage = $friendlyPhrase->render();
        $errorMessage = null;

        $exceptionClass = get_class($exception);

        switch ($exceptionClass) {
            case ApiException\AuthenticationException::class:
                $errorMessage = __(
                    '%1 Please verify your configured Company Code and Trusted ID are correct.',
                    $friendlyMessage
                );
                break;
            case ApiException\ConnectionFailureException::class:
                $errorMessage = __(
                    '%1 Vertex could not be reached. Please verify your configuration.',
                    $friendlyMessage
                );
                break;
            case ApiException::class:
            default:
                $errorMessage = __('%1 Error has been logged.', $friendlyMessage);
                break;
        }

        $this->messageManager->addErrorMessage($errorMessage);
    }
}
