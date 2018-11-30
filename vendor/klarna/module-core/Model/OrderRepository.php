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
use Klarna\Core\Api\OrderRepositoryInterface;
use Klarna\Core\Model\ResourceModel\Order as OrderResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface as MageOrder;

/**
 * Class OrderRepository
 *
 * @package Klarna\Core\Model
 */
class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Order factory
     *
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * Resource model
     *
     * @var OrderResource
     */
    private $resourceModel;

    /**
     * OrderRepository constructor.
     *
     * @param OrderFactory  $orderFactory
     * @param OrderResource $resourceModel
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        OrderFactory $orderFactory,
        OrderResource $resourceModel
    ) {
        $this->orderFactory = $orderFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function save(OrderInterface $order)
    {
        try {
            $this->resourceModel->save($order);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getByKlarnaOrderId($klarnaOrderId)
    {
        $order = $this->orderFactory->create();

        $orderId = $this->resourceModel->getIdByKlarnaOrderId($klarnaOrderId);
        if (!$orderId) {
            $order->setKlarnaOrderId($klarnaOrderId);
            return $order;
        }
        $this->resourceModel->load($order, $orderId);
        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getByOrder(MageOrder $mageOrder)
    {
        $order = $this->orderFactory->create();

        $orderId = $this->resourceModel->getIdByOrder($mageOrder);
        if (!$orderId) {
            throw new NoSuchEntityException(__('Requested order doesn\'t exist'));
        }
        $this->resourceModel->load($order, $orderId);
        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $order = $this->orderFactory->create();
        $this->resourceModel->load($order, $id);
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $id));
        }
        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getByReservationId($reservationId)
    {
        $order = $this->orderFactory->create();

        $orderId = $this->resourceModel->getIdByReservationId($reservationId);
        if (!$orderId) {
            throw new NoSuchEntityException(__('Order with Reservation ID "%1" does not exist.', $reservationId));
        }
        $this->resourceModel->load($order, $orderId);
        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySessionId($sessionId)
    {
        $order = $this->orderFactory->create();

        $orderId = $this->resourceModel->getIdBySessionId($sessionId);
        if (!$orderId) {
            throw new NoSuchEntityException(__('Order with session_id "%1" does not exist.', $sessionId));
        }
        $this->resourceModel->load($order, $orderId);
        return $order;
    }
}
