<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Data;

use Magento\Framework\Model\AbstractModel;
use Vertex\Tax\Model\ResourceModel\OrderInvoiceStatus as ResourceModel;

/**
 * Model for storage of the invoice sent flag on the order level
 *
 * This model is primarily used to prevent double commits to the Tax Log while an Order goes through statuses
 */
class OrderInvoiceStatus extends AbstractModel
{
    const FIELD_ID = ResourceModel::FIELD_ID;
    const FIELD_SENT = ResourceModel::FIELD_SENT;

    /**
     * {@inheritdoc}
     *
     * MEQP2 Warning: Protected method.  Needed to override AbstractDb's _construct
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Retrieve ID of Order with sent status
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getId();
    }

    /**
     * Set ID of Order with sent status
     *
     * @param int $orderId
     * @return OrderInvoiceStatus
     */
    public function setOrderId($orderId)
    {
        return $this->setId($orderId);
    }

    /**
     * Retrieve status of Order being sent to Vertex
     *
     * @return bool|null
     */
    public function isSent()
    {
        return $this->getData(static::FIELD_SENT);
    }

    /**
     * Set status of Order being sent to Vertex
     *
     * @param bool $isSent
     * @return OrderInvoiceStatus
     */
    public function setIsSent($isSent)
    {
        return $this->setData(static::FIELD_SENT, (bool)$isSent);
    }
}
