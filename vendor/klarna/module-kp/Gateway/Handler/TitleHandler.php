<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Gateway\Handler;

use Magento\Payment\Gateway\Config\ValueHandlerInterface;
use Magento\Payment\Model\InfoInterface;

/**
 * Class TitleHandler
 *
 * @package Klarna\Kp\Gateway\Handler
 */
class TitleHandler implements ValueHandlerInterface
{
    const DEFAULT_TITLE        = 'Klarna Payments';
    const DEFAULT_TITLE_FORMAT = '%s (%s)';

    /**
     * Retrieve method configured value
     *
     * @param array    $subject
     * @param int|null $storeId
     *
     * @return mixed
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function handle(array $subject, $storeId = null)
    {
        if (!isset($subject['payment'])) {
            return self::DEFAULT_TITLE;
        }
        /** @var InfoInterface $payment */
        $payment = $subject['payment']->getPayment();
        $title = $this->getTitle($payment);

        return $title;
    }

    /**
     * Get title for specified payment method
     *
     * @param InfoInterface $payment
     * @return string
     */
    public function getTitle($payment)
    {
        if ($payment->hasAdditionalInformation('method_title')) {
            return $payment->getAdditionalInformation('method_title');
        }
        if ($payment->hasAdditionalInformation('method_code')) {
            return sprintf(
                self::DEFAULT_TITLE_FORMAT,
                self::DEFAULT_TITLE,
                $payment->hasAdditionalInformation('method_code')
            );
        }
        return self::DEFAULT_TITLE;
    }
}
