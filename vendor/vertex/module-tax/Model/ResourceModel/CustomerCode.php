<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Vertex\Tax\Model\ResourceModel\CustomerCode\Collection;
use Vertex\Tax\Model\ResourceModel\CustomerCode\CollectionFactory;

/**
 * Performs Datastore-related actions for the CustomerCode repository
 */
class CustomerCode extends AbstractDb
{
    const TABLE = 'vertex_customer_code';

    const FIELD_ID = 'customer_id';
    const FIELD_CODE = 'customer_code';

    /** @var CollectionFactory */
    private $collectionFactory;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param string|null $connectionName
     */
    public function __construct(Context $context, CollectionFactory $collectionFactory, $connectionName = null)
    {
        parent::__construct($context, $connectionName);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     *
     * MEQP2 Warning: Protected method.  Needed to override AbstractDb's _construct
     */
    protected function _construct()
    {
        $this->_isPkAutoIncrement = false;
        $this->_init(static::TABLE, static::FIELD_ID);
    }

    /**
     * Retrieve a list of Customer Codes indexed by Customer ID
     *
     * @param int[] $customerIds
     * @return \Vertex\Tax\Model\Data\CustomerCode[]
     */
    public function getArrayByCustomerIds($customerIds)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(static::FIELD_ID, ['in' => $customerIds]);
        $collection->load();

        $result = [];
        foreach ($collection->getItems() as $item) {
            /** @var \Vertex\Tax\Model\Data\CustomerCode $item */
            $result[$item->getCustomerId()] = $item;
        }

        return $result;
    }
}
