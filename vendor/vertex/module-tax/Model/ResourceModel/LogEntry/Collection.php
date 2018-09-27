<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ResourceModel\LogEntry;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Vertex\Tax\Model\Data\LogEntry as Model;
use Vertex\Tax\Model\ResourceModel\LogEntry as ResourceModel;

/**
 * Collection of Log Entries
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     *
     * MEQP2 Warning: Protected method.  Needed to override AbstractDb's _construct
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
