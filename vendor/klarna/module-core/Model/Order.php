<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model;

use Klarna\Core\Api\OrderInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Order
 *
 * @package Klarna\Core\Model
 */
class Order extends AbstractModel implements OrderInterface, IdentityInterface
{
    const CACHE_TAG = 'klarna_core_order';

    /**
     * Get Identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getKlarnaOrderId()
    {
        return $this->_getData('klarna_order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->_getData('order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        $this->setData('order_id', $orderId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReservationId()
    {
        return $this->_getData('reservation_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setReservationId($reservationId)
    {
        $this->setData('reservation_id', $reservationId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionId()
    {
        return $this->_getData('session_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setSessionId($sessionId)
    {
        $this->setData('session_id', $sessionId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setKlarnaOrderId($orderId)
    {
        $this->setData('klarna_order_id', $orderId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAcknowledged($acknowledged)
    {
        $this->setData('is_acknowledged', $acknowledged);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAcknowledged()
    {
        return $this->_getData('is_acknowledged');
    }

    /**
     * {@inheritdoc}
     */
    public function isAcknowledged()
    {
        return (bool)$this->_getData('is_acknowledged');
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct()
    {
        $this->_init(\Klarna\Core\Model\ResourceModel\Order::class);
    }
}
