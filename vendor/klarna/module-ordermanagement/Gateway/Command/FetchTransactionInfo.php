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

use Magento\Payment\Gateway\Command;

/**
 * Class FetchTransactionInfo
 *
 * @package Klarna\Ordermanagement\Gateway\Command
 */
class FetchTransactionInfo extends AbstractCommand
{
    const PENDING = 0;
    const ACCEPT  = 1;
    const DENY    = -1;

    /**
     * FetchTransactionInfo command
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
        $order = $payment->getOrder();
        $store = $order->getStore();

        if ($this->helper->getVersionConfig($store)->isPaymentReview()) {
            $klarnaOrder = $this->klarnaOrderRepository->getByOrder($order);
            $transactionId = $klarnaOrder->getReservationId();

            $orderStatus = $this->getOmApi($order)->getFraudStatus($transactionId);

            if ($orderStatus === self::ACCEPT) {
                $payment->setIsTransactionApproved(true);
            } elseif ($orderStatus === self::DENY) {
                $payment->setIsTransactionDenied(true);
                $payment->getAuthorizationTransaction()->closeAuthorization();
            }
        }
        return null;
    }
}
