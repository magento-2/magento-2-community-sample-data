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
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getTagLine();

    /**
     * @return string
     */
    public function getLogoUrl();
}
