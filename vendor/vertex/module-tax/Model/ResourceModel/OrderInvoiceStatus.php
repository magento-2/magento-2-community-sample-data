<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Performs Datastore-related actions for the OrderInvoiceStatus repository
 */
class OrderInvoiceStatus extends AbstractDb
{
    const TABLE_NAME = 'vertex_order_invoice_status';
    const FIELD_ID = 'order_id';
    const FIELD_SENT = 'sent_to_vertex';

    /**
     * {@inheritdoc}
     *
     * MEQP2 Warning: Protected method.  Needed to override AbstractDb's _construct
     */
    protected function _construct()
    {
        $this->_isPkAutoIncrement = false;
        $this->_init(static::TABLE_NAME, static::FIELD_ID);
    }
}
