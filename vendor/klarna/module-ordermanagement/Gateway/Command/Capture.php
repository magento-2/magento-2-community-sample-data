<?php
/**
 * This file is part of the Klarna Order Management module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Ordermanagement\Gateway\Command;

use Klarna\Core\Exception as KlarnaException;
use Magento\Framework\DataObject;
use Magento\Payment\Gateway\Command;
use Klarna\Core\Helper\KlarnaConfig;
use Klarna\Core\Model\OrderRepository as KlarnaOrderRepository;
use Klarna\Ordermanagement\Model\Api\Factory;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Quote\Model\QuoteRepository as MageQuoteRepository;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository as MageOrderRepository;
use Magento\Framework\App\RequestInterface;

/**
 * Class Capture
 *
 * @package Klarna\Ordermanagement\Gateway\Command
 */
class Capture extends AbstractCommand
{

    /**
     * Capture constructor.
     *
     * @param KlarnaOrderRepository $kOrderRepository
     * @param MageQuoteRepository $mageQuoteRepository
     * @param MageOrderRepository $mageOrderRepository
     * @param KlarnaConfig $helper
     * @param Factory $omFactory
     * @param MessageManager $messageManager
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        KlarnaOrderRepository $kOrderRepository,
        MageQuoteRepository $mageQuoteRepository,
        MageOrderRepository $mageOrderRepository,
        KlarnaConfig $helper,
        Factory $omFactory,
        MessageManager $messageManager,
        RequestInterface $request,
        array $data = []
    ) {
        parent::__construct(
            $kOrderRepository,
            $mageQuoteRepository,
            $mageOrderRepository,
            $helper,
            $omFactory,
            $messageManager,
            $data
        );

        $this->request = $request;
    }

    /**
     * Capture command
     *
     * @param array $commandSubject
     *
     * @return null|Command\ResultInterface
     * @throws KlarnaException
     * @throws \Klarna\Core\Model\Api\Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(array $commandSubject)
    {
        $requestData =  $this->request->getPost();
        /** @var \Magento\Payment\Model\InfoInterface $payment */
        $payment = $commandSubject['payment']->getPayment();
        $amount = $commandSubject['amount'];

        $klarnaOrder = $this->getKlarnaOrder($payment->getOrder());

        if (!$klarnaOrder->getId() || !$klarnaOrder->getReservationId()) {
            throw new KlarnaException(__('Unable to capture payment for this order.'));
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        // if tracking info is presented but invalid, stop capture
        if (isset($requestData['tracking']) && !$this->isTrackingInfoValid($requestData['tracking'])) {
            return null;
        }
        /** @var \Magento\Store\Model\Store $store */
        $response = $this->getOmApi($order)
            ->capture($klarnaOrder->getReservationId(), $amount, $payment->getInvoice());

        if (!$response->getIsSuccessful()) {
            $errorMessage = __('Payment capture failed, please try again.');
            if ($response->getErrorCode() === 'CAPTURE_NOT_ALLOWED') {
                //TODO: Implement https://developers.klarna.com/api/#get-all-captures-for-one-order
                $errorMessage = __('Payment capture not allowed.');
            }

            $errorMessage = $this->getFullErrorMessage($response, $errorMessage, 'capture');
            throw new KlarnaException($errorMessage);
        }

        if (!$response->getTransactionId()) {
            return null;
        }
        $payment->setTransactionId($response->getTransactionId());

        if ($this->isProcessingShipment($requestData, $response)) {
            $this->addShippingInfoToCapture(
                $response->getCaptureId(),
                $klarnaOrder->getReservationId(),
                $requestData['tracking'],
                $order,
                $payment->getInvoice()
            );
        }
        return null;
    }

    /**
     * Add shipping info to capture
     *
     * @param string $captureId
     * @param string $klarnaOrderId
     * @param array $trackingData
     * @param OrderInterface $order
     * @param InvoiceInterface $invoice
     *
     * @return void
     */
    private function addShippingInfoToCapture($captureId, $klarnaOrderId, $trackingData, $order, $invoice)
    {
        $response = $this->getOmApi($order)
            ->addShippingInfo($klarnaOrderId, $captureId, $trackingData);

        if ($response->getIsSuccessful()) {
            $invoice->addComment("Shipping info sent to Klarna API", false, false);
            return;
        }
        foreach ($response->getErrorMessages() as $message) {
            $invoice->addComment($message, false, false);
        }
    }

    /**
     * Check if we are also processing a shipment with this invoice
     *
     * @param array $requestData
     * @param DataObject $response
     * @return bool
     */
    private function isProcessingShipment($requestData, $response)
    {
        if (isset($requestData['invoice']['do_shipment'])
            && $requestData['invoice']['do_shipment'] == true
            && $response->getCaptureId()
            && isset($requestData['tracking'])
            && $this->isTrackingInfoValid($requestData['tracking'])) {
            return true;
        }
        return false;
    }

    /**
     * vaidate tracking info
     *
     * @param array $trackingInfo
     * @return bool
     */
    private function isTrackingInfoValid($trackingInfo)
    {
        foreach ($trackingInfo as $var) {
            if (empty($var['carrier_code']) || empty($var['title']) || empty($var['number'])) {
                return false;
            }
        }
        return true;
    }
}
