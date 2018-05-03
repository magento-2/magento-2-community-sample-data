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
use Vertex\Tax\Model\Data\CustomerCode;
use Vertex\Tax\Model\Data\CustomerCodeFactory;
use Vertex\Tax\Model\ResourceModel\CustomerCode as ResourceModel;

/**
 * Repository of Vertex Customer Codes
 */
class CustomerCodeRepository
{
    /** @var ResourceModel */
    private $resourceModel;

    /** @var CustomerCodeFactory */
    private $factory;

    /**
     * @param ResourceModel $resourceModel
     * @param CustomerCodeFactory $factory
     */
    public function __construct(ResourceModel $resourceModel, CustomerCodeFactory $factory)
    {
        $this->resourceModel = $resourceModel;
        $this->factory = $factory;
    }

    /**
     * Save a Customer Code
     *
     * @param CustomerCode $customerCode
     * @return $this
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     */
    public function save(CustomerCode $customerCode)
    {
        try {
            $this->resourceModel->save($customerCode);
        } catch (AlreadyExistsException $e) {
            throw $e;
        } catch (\Exception $originalException) {
            throw new CouldNotSaveException(__('Unable to save Customer Code'), $originalException);
        }
        return $this;
    }

    /**
     * Delete a Customer Code
     *
     * @param CustomerCode $customerCode
     * @return $this
     * @throws CouldNotDeleteException
     */
    public function delete(CustomerCode $customerCode)
    {
        try {
            $this->resourceModel->delete($customerCode);
        } catch (\Exception $originalException) {
            throw new CouldNotDeleteException(__('Unable to delete Customer Code'), $originalException);
        }
        return $this;
    }

    /**
     * Delete a Customer Code given a Customer ID
     *
     * @param int $customerId
     * @return $this
     * @throws CouldNotDeleteException
     */
    public function deleteByCustomerId($customerId)
    {
        /** @var CustomerCode $customerCode */
        $customerCode = $this->factory->create();
        $customerCode->setId($customerId);
        try {
            $this->resourceModel->delete($customerCode);
        } catch (\Exception $originalException) {
            throw new CouldNotDeleteException(__('Unable to delete Customer Code'), $originalException);
        }
        return $this;
    }

    /**
     * Retrieve a Customer Code given a Customer ID
     *
     * @param int $customerId
     * @return CustomerCode
     * @throws NoSuchEntityException
     */
    public function getByCustomerId($customerId)
    {
        /** @var CustomerCode $customerCode */
        $customerCode = $this->factory->create();
        $this->resourceModel->load($customerCode, $customerId);
        if (!$customerCode->getId()) {
            throw NoSuchEntityException::singleField('customerId', $customerId);
        }
        return $customerCode;
    }

    /**
     * Retrieve an array of Customer Code's indexed by Customer ID
     *
     * @param int[] $customerIds
     * @return CustomerCode[] Indexed by Customer ID
     */
    public function getListByCustomerIds(array $customerIds)
    {
        return $this->resourceModel->getArrayByCustomerIds($customerIds);
    }
}
