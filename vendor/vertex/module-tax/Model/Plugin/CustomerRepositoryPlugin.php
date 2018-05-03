<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerExtensionInterface;
use Magento\Customer\Api\Data\CustomerExtensionInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerSearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Model\Config\State;
use Vertex\Tax\Model\Data\CustomerCode;
use Vertex\Tax\Model\Data\CustomerCodeFactory;
use Vertex\Tax\Model\Repository\CustomerCodeRepository;

/**
 * Adds CustomerCode extension attribute to Customer Repository
 *
 * @see CustomerRepositoryInterface
 */
class CustomerRepositoryPlugin
{
    /** @var CustomerCodeRepository */
    private $repository;

    /** @var CustomerExtensionInterfaceFactory */
    private $extensionFactory;

    /** @var CustomerCodeFactory */
    private $codeFactory;

    /** @var LoggerInterface */
    private $logger;

    /** @var bool[] */
    private $currentlySaving = [];

    /** @var State */
    private $state;

    /**
     * @param CustomerCodeRepository $repository
     * @param CustomerExtensionInterfaceFactory $extensionFactory
     * @param CustomerCodeFactory $codeFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        State $state,
        CustomerCodeRepository $repository,
        CustomerExtensionInterfaceFactory $extensionFactory,
        CustomerCodeFactory $codeFactory,
        LoggerInterface $logger
    ) {
        $this->state = $state;
        $this->repository = $repository;
        $this->extensionFactory = $extensionFactory;
        $this->codeFactory = $codeFactory;
        $this->logger = $logger;
    }

    /**
     * Add the Vertex Customer Code to the Customer extension attribute when customers are retrieved from the repository
     *
     * @param CustomerRepositoryInterface $subject
     * @param CustomerSearchResultsInterface $results
     * @see CustomerRepositoryInterface::getList()
     * @return CustomerSearchResultsInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(CustomerRepositoryInterface $subject, $results)
    {
        if (!$this->state->isActive()) {
            return $results;
        }

        if ($results->getTotalCount() <= 0) {
            return $results;
        }

        $customerIds = array_map(
            function (CustomerInterface $customer) {
                return $customer->getId();
            },
            $results->getItems()
        );

        $customerCodes = $this->repository->getListByCustomerIds($customerIds);

        foreach ($results->getItems() as $customer) {
            if (!isset($customerCodes[$customer->getId()])) {
                continue;
            }

            $extensionAttributes = $this->getExtensionAttributes($customer);
            $extensionAttributes->setVertexCustomerCode($customerCodes[$customer->getId()]);
        }

        return $results;
    }

    /**
     * Add the Vertex Customer Code to the Customer extension attribute when a customer is retrieved from the repository
     *
     * @see CustomerRepositoryInterface::getById()
     *
     * @param CustomerRepositoryInterface $subject
     * @param CustomerInterface $result
     * @return CustomerInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetById(CustomerRepositoryInterface $subject, CustomerInterface $result)
    {
        if (!$this->state->isActive()) {
            return $result;
        }

        if ($this->isCurrentlySaving($result)) {
            return $result;
        }

        $extensionAttributes = $this->getExtensionAttributes($result);

        try {
            $customerCode = $this->repository->getByCustomerId($result->getId());
            $extensionAttributes->setVertexCustomerCode($customerCode->getCustomerCode());
        } catch (NoSuchEntityException $exception) {
            $extensionAttributes->setVertexCustomerCode(null);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }

        return $result;
    }

    /**
     * Add the Vertex Customer Code to the Customer extension attribute when a customer is retrieved from the repository
     *
     * @see CustomerRepositoryInterface::get()
     *
     * @param CustomerRepositoryInterface $subject
     * @param CustomerInterface $result
     * @return CustomerInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(CustomerRepositoryInterface $subject, CustomerInterface $result)
    {
        return $this->afterGetById($subject, $result);
    }

    /**
     * Save the Vertex Customer Code when the Customer is saved
     *
     * @see CustomerRepositoryInterface::save()
     * @todo Convert to afterSave once we only support Magento 2.2+
     *
     * @param CustomerRepositoryInterface $subject
     * @param callable $proceed {@see CustomerRepositoryInterface::save()}
     * @param CustomerInterface $customer
     * @param string|null $passwordHash
     * @return CustomerInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        CustomerRepositoryInterface $subject,
        callable $proceed,
        CustomerInterface $customer,
        $passwordHash = null
    ) {
        $this->setCurrentlySaving($customer);

        if ($customer->getExtensionAttributes()) {
            $customerCode = $customer->getExtensionAttributes()->getVertexCustomerCode();
            /** @var CustomerInterface $result */
            $result = $proceed($customer, $passwordHash);

            if (!$this->state->isActive()) {
                return $result;
            }

            if ($customerCode) {
                $codeModel = $this->getCodeModel($result->getId());
                $codeModel->setCustomerCode($customerCode);
                try {
                    $this->repository->save($codeModel);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                }
            } else {
                $this->deleteByCustomerId($result->getId());
            }
            $extensionAttributes = $this->getExtensionAttributes($result);
            $extensionAttributes->setVertexCustomerCode($customerCode);
        } else {
            $result = $proceed($customer, $passwordHash);
        }
        $this->unsetCurrentlySaving($result);
        return $result;
    }

    /**
     * Delete the Vertex Customer Code when the customer is deleted
     *
     * @see CustomerRepositoryInterface::delete()
     * @todo Convert to afterDelete once we only support Magento 2.2+
     *
     * @param CustomerRepositoryInterface $subject
     * @param callable $proceed {@see CustomerRepositoryInterface::delete()}
     * @param CustomerInterface $customer
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDelete(CustomerRepositoryInterface $subject, callable $proceed, CustomerInterface $customer)
    {
        $customerId = $customer->getId();
        $result = $proceed($customer);

        if (!$this->state->isActive()) {
            return $result;
        }

        if ($result) {
            $this->deleteByCustomerId($customerId);
        }

        return $result;
    }

    /**
     * Delete the Vertex Customer code when the customer is deleted
     *
     * @see CustomerRepositoryInterface::deleteById()
     * @todo Convert to afterDeleteById once we only support Magento 2.2+
     *
     * @param CustomerRepositoryInterface $subject
     * @param callable $proceed {@see CustomerRepositoryInterface::deleteById()}
     * @param int $customerId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDeleteById(CustomerRepositoryInterface $subject, callable $proceed, $customerId)
    {
        $result = $proceed($customerId);

        if (!$this->state->isActive()) {
            return $result;
        }

        if ($result) {
            $this->deleteByCustomerId($customerId);
        }

        return $result;
    }

    /**
     * Retrieve the Customer Code by Customer ID
     *
     * @param int $customerId
     * @return \Vertex\Tax\Model\Data\CustomerCode
     */
    private function getCodeModel($customerId)
    {
        try {
            $customerCode = $this->repository->getByCustomerId($customerId);
        } catch (NoSuchEntityException $e) {
            /** @var CustomerCode $customerCode */
            $customerCode = $this->codeFactory->create();
            $customerCode->setCustomerId($customerId);
        }
        return $customerCode;
    }

    /**
     * Delete a Customer Code given a Customer ID
     *
     * @param int $customerId
     * @return void
     */
    private function deleteByCustomerId($customerId)
    {
        try {
            $this->repository->deleteByCustomerId($customerId);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }
    }

    /**
     * Get a CustomerExtensionInterface object, creating it if it is not yet created
     *
     * @param CustomerInterface $customer
     * @return CustomerExtensionInterface
     */
    private function getExtensionAttributes(CustomerInterface $customer)
    {
        $extensionAttributes = $customer->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionFactory->create();
            $customer->setExtensionAttributes($extensionAttributes);
        }

        return $extensionAttributes;
    }

    /**
     * Set whether or not we are currently saving a specific customer
     *
     * This is used to prevent loading the attribute during a save procedure
     *
     * @param CustomerInterface $customer
     */
    private function setCurrentlySaving(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            $this->currentlySaving[$customer->getId()] = true;
        }
    }

    /**
     * Determine whether or not we are currently saving a specific customer
     *
     * This is used to prevent loading the attribute during a save procedure
     *
     * @param CustomerInterface $customer
     * @return bool
     */
    private function isCurrentlySaving(CustomerInterface $customer)
    {
        return isset($this->currentlySaving[$customer->getId()]);
    }

    /**
     * Declare that we are no longer currently saving a specific customer
     *
     * @param CustomerInterface $customer
     */
    private function unsetCurrentlySaving(CustomerInterface $customer)
    {
        unset($this->currentlySaving[$customer->getId()]);
    }
}
