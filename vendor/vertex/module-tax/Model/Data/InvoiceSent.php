<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Data;

use Magento\Framework\Model\AbstractModel;
use Vertex\Tax\Model\ResourceModel\InvoiceSent as ResourceModel;

/**
 * Model for storage of the invoice sent flag
 *
 * This model is primarily used to prevent accidental double commits to the Tax Log
 */
class InvoiceSent extends AbstractModel
{
    const FIELD_ID = ResourceModel::FIELD_ID;
    const FIELD_SENT = ResourceModel::FIELD_SENT;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get the Invoice ID
     *
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->getId();
    }

    /**
     * Set the Invoice ID
     *
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceId($invoiceId)
    {
        return $this->setId($invoiceId);
    }

    /**
     * Get whether or not the invoice was committed to Vertex
     *
     * @return bool
     */
    public function isSent()
    {
        return (bool)$this->getData(static::FIELD_SENT);
    }

    /**
     * Set whether or not the invoice was committed to Vertex
     *
     * @param bool $isSent
     * @return $this
     */
    public function setIsSent($isSent)
    {
        return $this->setData(static::FIELD_SENT, $isSent);
    }
}
