<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Api;

/**
 * Interface VersionInterface
 *
 * @package Klarna\Core\Api
 */
interface VersionInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return bool
     */
    public function isShippingCallbackSupport();

    /**
     * @return bool
     */
    public function isMerchantCheckboxSupport();

    /**
     * @return bool
     */
    public function isDateOfBirthMandatorySupport();

    /**
     * @return bool
     */
    public function isPhoneMandatorySupport();

    /**
     * @return string
     */
    public function getOrdermanagement();

    /**
     * @return bool
     */
    public function isTitleMandatorySupport();

    /**
     * @return bool
     */
    public function isDelayedPushNotification();

    /**
     * @return bool
     */
    public function isPartialPaymentDisabled();

    /**
     * @return bool
     */
    public function isSeparateTaxLine();

    /**
     * @return bool
     */
    public function isShippingInIframe();

    /**
     * @param bool $testmode
     * @return string
     */
    public function getUrl($testmode = true);

    /**
     * @return bool
     */
    public function isPaymentReview();

    /**
     * @return bool
     */
    public function isPackstationSupport();

    /**
     * @return bool
     */
    public function isCartTotalsInIframe();
}
