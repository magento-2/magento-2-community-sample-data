<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Api;

use Magento\Sales\Api\Data\OrderInterface as MageOrder;

/**
 * Interface OrderRepositoryInterface
 *
 * @package Klarna\Core\Api
 */
interface OrderRepositoryInterface
{
    /**
     * Save an order
     *
     * @param OrderInterface $order
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderInterface $order);

    /**
     * Get order by ID
     *
     * @param int $id
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function getById($id);

    /**
     * Load by Klarna order id
     *
     * @param string $klarnaOrderId
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByKlarnaOrderId($klarnaOrderId);

    /**
     * Load by session id
     *
     * @param string $sessionId
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySessionId($sessionId);

    /**
     * Load by reservation id
     *
     * @param string $reservationId
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByReservationId($reservationId);

    /**
     * Load by an order
     *
     * @param MageOrder $mageOrder
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByOrder(MageOrder $mageOrder);
}
