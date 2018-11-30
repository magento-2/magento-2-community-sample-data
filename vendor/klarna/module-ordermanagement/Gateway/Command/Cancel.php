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

use Klarna\Core\Api\OrderInterface as KlarnaOrderInterface;
use Klarna\Core\Exception as KlarnaException;
use Magento\Payment\Gateway\Command;
use Magento\Sales\Api\Data\OrderInterface as MageOrderInterface;

/**
 * Class Cancel
 *
 * @package Klarna\Ordermanagement\Gateway\Command
 */
class Cancel extends AbstractCommand
{
    /**
     * Cancel command
     *
     * @param array $commandSubject
     *
     * @return null|Command\ResultInterface
     * @throws \Klarna\Core\Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(array $commandSubject)
    {
        /** @var \Magento\Payment\Model\InfoInterface $payment */
        $payment = $commandSubject['payment']->getPayment();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        $klarnaOrder = $this->getKlarnaOrder($order);

        if (!$klarnaOrder->getId() || !$klarnaOrder->getReservationId()) {
            throw new KlarnaException(__('Unable to cancel payment for this order.'));
        }

        $response = $this->processPayment($order, $klarnaOrder, $payment);

        if (!$response->getIsSuccessful()) {
            $errorMessage = __('Order cancellation failed, please try again.');
            $errorMessage = $this->getFullErrorMessage($response, $errorMessage, 'cancel');
            throw new KlarnaException($errorMessage);
        }

        if ($response->getTransactionId()) {
            $payment->setTransactionId($response->getTransactionId());
        }
        return null;
    }

    /**
     * Process cancel/refund for order
     *
     * @param MageOrderInterface   $order
     * @param KlarnaOrderInterface $klarnaOrder
     * @return \Magento\Framework\DataObject
     */
    private function processPayment($order, $klarnaOrder)
    {
        if ($order->hasInvoices()) {
            return $this->getOmApi($order)->release($klarnaOrder->getReservationId());
        }

        return $this->getOmApi($order)->cancel($klarnaOrder->getReservationId());
    }
}
