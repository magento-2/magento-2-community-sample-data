<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Sales\Api\Data\OrderInterface as MageOrder;

/**
 * Class Order
 *
 * @package Klarna\Core\Model\ResourceModel
 */
class Order extends AbstractDb
{
    /**
     * Get order identifier by Reservation
     *
     * @param string $reservationId
     * @return false|int
     */
    public function getIdByReservationId($reservationId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'id')
                             ->where('reservation_id = :reservation_id');

        $bind = [':reservation_id' => (string)$reservationId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get order identifier by Klarna Order ID
     *
     * @param string $klarnaOrderId
     * @return int|false
     */
    public function getIdByKlarnaOrderId($klarnaOrderId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'id')
                             ->where('klarna_order_id = :klarna_order_id');

        $bind = [':klarna_order_id' => (string)$klarnaOrderId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get order identifier by order
     *
     * @param MageOrder $mageOrder
     * @return false|int
     */
    public function getIdByOrder(MageOrder $mageOrder)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'id')
                             ->where('order_id = :order_id');

        $bind = [':order_id' => (string)$mageOrder->getId()];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get order ID by Session ID
     *
     * @param string $sessionId
     * @return string
     */
    public function getIdBySessionId($sessionId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'id')
                             ->where('session_id = :session_id');

        $bind = [':session_id' => (string)$sessionId];

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
        $this->_init('klarna_core_order', 'id');
    }
}
