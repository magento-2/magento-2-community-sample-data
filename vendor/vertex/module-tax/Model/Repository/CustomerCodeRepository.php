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
use Vertex\Tax\Model\Registry\CustomerCodeRegistry;
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

    /** @var CustomerCodeRegistry */
    private $registry;

    /**
     * @param ResourceModel $resourceModel
     * @param CustomerCodeFactory $factory
     * @param CustomerCodeRegistry $registry
     */
    public function __construct(
        ResourceModel $resourceModel,
        CustomerCodeFactory $factory,
        CustomerCodeRegistry $registry
    ) {
        $this->resourceModel = $resourceModel;
        $this->factory = $factory;
        $this->registry = $registry;
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
        // Exit early if code is already in the registry
        $registered = $this->registry->get($customerCode->getCustomerId());
        if ($registered === $customerCode->getCustomerCode()) {
            return $this;
        }

        try {
            $this->resourceModel->save($customerCode);
            $this->registry->set($customerCode->getCustomerId(), $customerCode->getCustomerCode());
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
        return $this->deleteByCustomerId($customerCode->getCustomerId());
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
            $this->registry->delete($customerId);
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

        $registered = $this->registry->get($customerId);
        if ($registered === null) {
            throw NoSuchEntityException::singleField('customerId', $customerId);
        }
        if ($registered !== false) {
            $customerCode->setCustomerId($customerId);
            $customerCode->setCustomerCode($registered);
            return $customerCode;
        }

        $this->resourceModel->load($customerCode, $customerId);
        if (!$customerCode->getId()) {
            $this->registry->set($customerId, null);
            throw NoSuchEntityException::singleField('customerId', $customerId);
        }
        $this->registry->set($customerCode->getCustomerId(), $customerCode->getCustomerCode());
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
        $unregisteredIds = [];
        $registryCustomerCodes = [];
        foreach ($customerIds as $customerId) {
            $registered = $this->registry->get($customerId);
            if ($registered === false) {
                $unregisteredIds[] = $customerId;
            } else {
                $customerCode = $this->factory->create();
                $customerCode->setCustomerId($customerId);
                $customerCode->setCustomerCode($registered);
                $registryCustomerCodes[$customerId] = $customerCode;
            }
        }

        $dbCustomerCodes = [];
        if (!empty($unregisteredIds)) {
            $dbCustomerCodes = $this->resourceModel->getArrayByCustomerIds($unregisteredIds);
        }

        foreach ($dbCustomerCodes as $dbCustomerCode) {
            $this->registry->set($dbCustomerCode->getCustomerId(), $dbCustomerCode->getCustomerCode());
        }

        return array_replace($dbCustomerCodes, $registryCustomerCodes);
    }
}
