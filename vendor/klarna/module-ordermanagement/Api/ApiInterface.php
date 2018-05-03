<?php
/**
 * This file is part of the Klarna Order Management module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Ordermanagement\Api;

use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\InvoiceInterface as Invoice;
use Magento\Sales\Api\Data\CreditmemoInterface as CreditMemo;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Interface ApiInterface
 *
 * @package Klarna\Ordermanagement\Api
 */
interface ApiInterface
{
    /**
     * Capture an amount on an order
     *
     * @param string  $orderId
     * @param float   $amount
     * @param Invoice $invoice
     *
     * @return DataObject
     */
    public function capture($orderId, $amount, $invoice = null);

    /**
     * Refund for an order
     *
     * @param string     $orderId
     * @param float      $amount
     * @param Creditmemo $creditMemo
     *
     * @return DataObject
     */
    public function refund($orderId, $amount, $creditMemo = null);

    /**
     * Cancel an order
     *
     * @param string $orderId
     *
     * @return DataObject
     */
    public function cancel($orderId);

    /**
     * Release the authorization for an order
     *
     * @param string $orderId
     *
     * @return DataObject
     */
    public function release($orderId);

    /**
     * Acknowledge an order in order management
     *
     * @param string $orderId
     *
     * @return DataObject
     */
    public function acknowledgeOrder($orderId);

    /**
     * Update merchant references for a Klarna order
     *
     * @param string $orderId
     * @param string $reference1
     * @param string $reference2
     *
     * @return DataObject
     */
    public function updateMerchantReferences($orderId, $reference1, $reference2 = null);

    /**
     * Get the fraud status of an order to determine if it should be accepted or denied within Magento
     *
     * Return value of 1 means accept
     * Return value of 0 means still pending
     * Return value of -1 means deny
     *
     * @param string $orderId
     *
     * @return int
     */
    public function getFraudStatus($orderId);

    /**
     * Get order details for a completed Klarna order
     *
     * @param string $orderId
     *
     * @return DataObject
     */
    public function getPlacedKlarnaOrder($orderId);

    /**
     * Reset connection based on store config
     *
     * @param StoreInterface $store
     * @param string         $methodCode
     * @return $this
     */
    public function resetForStore($store, $methodCode);

    /**
     * Set builder type to use for API requests
     *
     * @param string $builderType
     * @return $this
     */
    public function setBuilderType($builderType);
}
