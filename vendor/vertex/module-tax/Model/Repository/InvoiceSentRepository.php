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
use Vertex\Tax\Model\Data\InvoiceSent;
use Vertex\Tax\Model\Data\InvoiceSentFactory;
use Vertex\Tax\Model\ResourceModel\InvoiceSent as ResourceModel;

/**
 * Repository of Invoice sent data
 */
class InvoiceSentRepository
{
    /** @var ResourceModel */
    private $resourceModel;

    /** @var InvoiceSentFactory */
    private $factory;

    /**
     * @param ResourceModel $resourceModel
     * @param InvoiceSentFactory $factory
     */
    public function __construct(ResourceModel $resourceModel, InvoiceSentFactory $factory)
    {
        $this->resourceModel = $resourceModel;
        $this->factory = $factory;
    }

    /**
     * Save an InvoiceSent object
     *
     * @param InvoiceSent $invoiceSent
     * @return $this
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(InvoiceSent $invoiceSent)
    {
        try {
            $this->resourceModel->save($invoiceSent);
        } catch (AlreadyExistsException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save Invoice Sent status'), $e);
        }
        return $this;
    }

    /**
     * Delete an InvoiceSent object
     *
     * @param InvoiceSent $invoiceSent
     * @return $this
     * @throws CouldNotDeleteException
     */
    public function delete(InvoiceSent $invoiceSent)
    {
        try {
            $this->resourceModel->delete($invoiceSent);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to delete Invoice Sent status'), $e);
        }
        return $this;
    }

    /**
     * Delete an InvoiceSent object given an Invoice ID
     *
     * @param int $invoiceId
     * @return $this
     * @throws CouldNotDeleteException
     */
    public function deleteByInvoiceId($invoiceId)
    {
        /** @var InvoiceSent $invoiceSent */
        $invoiceSent = $this->factory->create();
        $invoiceSent->setId($invoiceId);
        try {
            $this->resourceModel->delete($invoiceSent);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to delete Invoice Sent status'), $e);
        }
        return $this;
    }

    /**
     * Retrieve an InvoiceSent object for an Invoice
     *
     * @param int $invoiceId
     * @return InvoiceSent
     * @throws NoSuchEntityException
     */
    public function getByInvoiceId($invoiceId)
    {
        $invoiceSent = $this->factory->create();
        $this->resourceModel->load($invoiceSent, $invoiceId);
        if (!$invoiceSent->getId()) {
            throw NoSuchEntityException::singleField('invoiceId', $invoiceId);
        }
        return $invoiceSent;
    }
}
