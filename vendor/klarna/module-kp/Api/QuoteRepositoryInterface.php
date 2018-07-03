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

use Magento\Quote\Api\Data\CartInterface as MageQuoteInterface;

/**
 * Interface QuoteRepositoryInterface
 *
 * @package Klarna\Kp\Api
 */
interface QuoteRepositoryInterface
{
    /**
     * @param QuoteInterface $page
     * @return QuoteInterface
     */
    public function save(QuoteInterface $page);

    /**
     * @param int  $id
     * @param bool $forceReload
     * @return QuoteInterface
     */
    public function getById($id, $forceReload = false);

    /**
     * @param MageQuoteInterface $mageQuote
     * @return QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getActiveByQuote(MageQuoteInterface $mageQuote);

    /**
     * @param QuoteInterface $page
     * @return void
     */
    public function delete(QuoteInterface $page);

    /**
     * @param int $id
     * @return void
     */
    public function deleteById($id);

    /**
     * Mark quote as inactive and cancel it with API
     *
     * @param QuoteInterface $quote
     */
    public function markInactive(QuoteInterface $quote);

    /**
     * Load quote by session_id
     *
     * @param string $sessionId
     * @param bool   $forceReload
     * @return QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySessionId($sessionId, $forceReload = false);
}
