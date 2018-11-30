<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

/**
 * Interface QuoteInterface
 *
 * @method getId():int
 */
interface QuoteInterface
{
    /**
     * Get the Klarna session_id
     *
     * @return string
     */
    public function getSessionId();

    /**
     * Get the Klarna client token
     *
     * @return string
     */
    public function getClientToken();

    /**
     * Get the Klarna Authorization token
     *
     * @return string
     */
    public function getAuthorizationToken();

    /**
     * Get whether the quote is active/inactive
     *
     * @return int
     * @deprecated 5.3.0
     * @see isActive
     */
    public function getIsActive();

    /**
     * Get whether the quote is active/inactive
     *
     * @return bool
     */
    public function isActive();

    /**
     * Set quote active/inactive
     *
     * @param int $active
     * @return $this
     */
    public function setIsActive($active);

    /**
     * Set client_token_id
     *
     * @param string $token
     * @return $this
     */
    public function setClientToken($token);

    /**
     * Set authorization token
     *
     * @param string $token
     * @return $this
     */
    public function setAuthorizationToken($token);

    /**
     * Set Klarna session_id
     *
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId($sessionId);

    /**
     * Get Magento Quote ID
     *
     * @return int
     */
    public function getQuoteId();

    /**
     * Set Magento Quote ID
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId);

    /**
     * Get Klarna Payment Methods
     *
     * @return string[]
     */
    public function getPaymentMethods();

    /**
     * Set Klarna Payment Methods
     *
     * @param string[]|string $methods
     * @return $this
     */
    public function setPaymentMethods($methods);

    /**
     * Set Klarna Payment Method Info
     *
     * @param string[] $methodinfo
     * @return $this
     */
    public function setPaymentMethodInfo($methodinfo);

    /**
     * Get Klarna Payment Method Info
     *
     * @return object[]
     */
    public function getPaymentMethodInfo();
}
