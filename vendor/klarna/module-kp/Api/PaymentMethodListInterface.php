<?php
/**
 * This file is part of the Klarna Kp module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

use Klarna\Kp\Model\Payment\Kp;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Interface PaymentMethodListInterface
 * @api
 * @package Klarna\Kp\Api
 */
interface PaymentMethodListInterface
{
    /**
     * Get list of Klarna payment types
     *
     * @param CartInterface $quote
     * @return string[]
     * @deprecated 5.3.0
     * @see getKlarnaMethodInfo
     */
    public function getKlarnaMethodCodes(CartInterface $quote = null);

    /**
     * Get list of Klarna payment types
     *
     * @param CartInterface $quote
     * @return string[]
     */
    public function getKlarnaMethodInfo(CartInterface $quote);

    /**
     * Get payment instance for specified Klarna payment method
     *
     * @param string $method
     * @return MethodInterface|Kp
     */
    public function getPaymentMethod($method);
}
