<?php
/**
 * This file is part of the Klarna Kp module
 *
 * (c) Klarna AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

/**
 * Interface KlarnaPaymentMethod
 *
 * @package Klarna\Kp\Model\Payment
 */
interface KlarnaPaymentMethodInterface
{
    /**
     * Set payment method code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get tag line
     *
     * @return string
     * @deprecated 5.3.0
     */
    public function getTagLine();

    /**
     * @return string
     * @deprecated 5.3.0
     */
    public function getLogoUrl();
}
