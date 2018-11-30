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
use Magento\Payment\Gateway\Command;

/**
 * Class Refund
 *
 * @package Klarna\Ordermanagement\Gateway\Command
 */
class Refund extends AbstractCommand
{
    /**
     * Refund command
     *
     * @param array $commandSubject
     *
     * @return null|Command\ResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(array $commandSubject)
    {
        /** @var \Magento\Payment\Model\InfoInterface $payment */
        $payment = $commandSubject['payment']->getPayment();
        $amount = $commandSubject['amount'];
        $order = $payment->getOrder();
        $klarnaOrder = $this->getKlarnaOrder($order);

        if (!$klarnaOrder->getId() || !$klarnaOrder->getReservationId()) {
            $e = new KlarnaException(__('Unable to refund payment for this order.'));
            $this->messageManager->addErrorMessage($e->getMessage());
            throw $e;
        }

        $response = $this->getOmApi($order)
                         ->refund($klarnaOrder->getReservationId(), $amount, $payment->getCreditmemo());

        if (!$response->getIsSuccessful()) {
            $errorMessage = __('Payment refund failed, please try again.');

            $errorMessage = $this->getFullErrorMessage($response, $errorMessage, 'refund');
            throw new KlarnaException($errorMessage);
        }

        if ($response->getTransactionId()) {
            $payment->setTransactionId($response->getTransactionId());
        }
        return null;
    }
}
