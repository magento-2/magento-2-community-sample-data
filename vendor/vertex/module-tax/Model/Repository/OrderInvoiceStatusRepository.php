<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Repository;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vertex\Tax\Model\ResourceModel\OrderInvoiceStatus as ResourceModel;
use Vertex\Tax\Model\Data\OrderInvoiceStatusFactory as Factory;
use Vertex\Tax\Model\Data\OrderInvoiceStatus;

/**
 * Repository of Order Invoice data
 */
class OrderInvoiceStatusRepository
{
    /** @var ResourceModel */
    private $resourceModel;

    /** @var Factory */
    private $factory;

    /**
     * @param ResourceModel $resourceModel
     * @param Factory $factory
     */
    public function __construct(ResourceModel $resourceModel, Factory $factory)
    {
        $this->resourceModel = $resourceModel;
        $this->factory = $factory;
    }

    /**
     * Save an OrderInvoiceStatus object
     *
     * @param OrderInvoiceStatus $orderInvoiceStatus
     * @return OrderInvoiceStatusRepository
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     */
    public function save(OrderInvoiceStatus $orderInvoiceStatus)
    {
        try {
            $this->resourceModel->save($orderInvoiceStatus);
        } catch (AlreadyExistsException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save Order Invoice Sent status'), $e);
        }
        return $this;
    }

    /**
     * Delete an OrderInvoiceStatus object
     *
     * @param OrderInvoiceStatus $orderInvoiceStatus
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(OrderInvoiceStatus $orderInvoiceStatus)
    {
        try {
            $this->resourceModel->delete($orderInvoiceStatus);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to delete Order Invoice Sent status'), $e);
        }
    }

    /**
     * Delete an OrderInvoiceStatus object given an Order ID
     *
     * @param int $orderId
     * @return OrderInvoiceStatusRepository
     * @throws CouldNotDeleteException
     */
    public function deleteByOrderId($orderId)
    {
        /** @var OrderInvoiceStatus $orderInvoiceStatus */
        $orderInvoiceStatus = $this->factory->create();
        $orderInvoiceStatus->setId($orderId);
        try {
            $this->resourceModel->delete($orderInvoiceStatus);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to delete Order Invoice Sent status'), $e);
        }
        return $this;
    }

    /**
     * Retrieve an OrderInvoiceStatus object for an Order
     *
     * @param int $orderId
     * @return OrderInvoiceStatus
     * @throws NoSuchEntityException
     */
    public function getByOrderId($orderId)
    {
        /** @var OrderInvoiceStatus $orderInvoiceStatus */
        $orderInvoiceStatus = $this->factory->create();
        $this->resourceModel->load($orderInvoiceStatus, $orderId);
        if (!$orderInvoiceStatus->getId()) {
            throw NoSuchEntityException::singleField('orderId', $orderId);
        }
        return $orderInvoiceStatus;
    }
}
