<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Data;

use Magento\Framework\Model\AbstractModel;
use Vertex\Tax\Model\ResourceModel\CustomerCode as ResourceModel;

/**
 * Model for storage of the Vertex Customer Code
 *
 * This model is used as the implementation for the vertex_customer_code extension attribute on the
 * {@see \Magento\Customer\Api\Data\CustomerInterface}
 */
class CustomerCode extends AbstractModel
{
    const FIELD_ID = ResourceModel::FIELD_ID;
    const FIELD_CODE = ResourceModel::FIELD_CODE;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get Vertex Customer Code
     *
     * @return string
     */
    public function getCustomerCode()
    {
        return $this->getData(static::FIELD_CODE);
    }

    /**
     * Set Vertex Customer Code
     *
     * @param string $customerCode
     * @return $this
     */
    public function setCustomerCode($customerCode)
    {
        return $this->setData(static::FIELD_CODE, $customerCode);
    }

    /**
     * Get Customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getId();
    }

    /**
     * Set Customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setId($customerId);
    }
}
