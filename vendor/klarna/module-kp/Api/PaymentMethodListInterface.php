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
 *
 * @package Klarna\Kp\Api
 */
interface PaymentMethodListInterface
{
    /**
     * @param CartInterface $quote
     * @return string[]
     */
    public function getKlarnaMethodCodes(CartInterface $quote = null);

    /**
     * @param $method
     * @return MethodInterface|Kp
     */
    public function getPaymentMethod($method);
}
