<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\ResourceModel;

use Magento\Quote\Api\Data\CartInterface as MageQuoteInterface;

/**
 * Class Quote
 *
 * @package Klarna\Kp\Model\ResourceModel
 */
class Quote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Get quote identifier by client_token
     *
     * @param string $clientToken
     * @return int|false
     */
    public function getIdByClientToken($clientToken)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'payments_quote_id')
                             ->where('client_token = :client_token');

        $bind = [':client_token' => (string)$clientToken];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get quote identifier by client_token
     *
     * @param string $sessionId
     * @return int|false
     */
    public function getIdBySessionId($sessionId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'payments_quote_id')
                             ->where('session_id = :session_id');

        $bind = [':session_id' => (string)$sessionId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get quote identifier by active Magento quote
     *
     * @param MageQuoteInterface $mageQuote
     * @return int|false
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getActiveByQuote(MageQuoteInterface $mageQuote)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'payments_quote_id')->where('is_active = 1')
                             ->where('quote_id = :quote_id');

        $bind = [':quote_id' => (string)$mageQuote->getId()];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct()
    {
        $this->_init('klarna_payments_quote', 'payments_quote_id');
    }
}
