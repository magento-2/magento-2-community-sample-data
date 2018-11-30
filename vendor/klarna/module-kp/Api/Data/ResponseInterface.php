<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api\Data;

/**
 * Interface ResponseInterface
 *
 * @package Klarna\Kp\Api\Data
 */
interface ResponseInterface
{
    /**
     * PENDING (0)
     */
    const FRAUD_PENDING = 0;

    /**
     * REJECTED (1)
     */
    const FRAUD_REJECTED = 1;

    /**
     * ACCEPTED (2)
     */
    const FRAUD_ACCEPTED = 2;

    /**
     * @return string
     */
    public function getSessionId();

    /**
     * @return string
     */
    public function getClientToken();

    /**
     * @return int
     */
    public function getResponseCode();

    /**
     * @return array
     */
    public function getPaymentMethodCategories();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @return int
     */
    public function getFraudStatus();

    /**
     * @return bool
     */
    public function isSuccessfull();

    /**
     * @return array
     */
    public function toArray();
}
