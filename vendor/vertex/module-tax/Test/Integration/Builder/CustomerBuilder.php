<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\Builder;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;

/**
 * Build a customer entity
 */
class CustomerBuilder
{
    /** @var CustomerInterfaceFactory */
    private $customerFactory;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /**
     * @param CustomerInterfaceFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerInterfaceFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Create a customer
     *
     * @param callable $customerConfiguration Receives 1 parameter of CustomerInterface.
     *      Should return a CustomerInterface.
     * @return CustomerInterface
     * @throws \TypeError
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCustomer(callable $customerConfiguration)
    {
        /** @var CustomerInterface $customer */
        $customer = $customerConfiguration($this->customerFactory->create());

        if (!($customer instanceof CustomerInterface)) {
            throw new \TypeError('Result of createCustomer callback must return a CustomerInterface');
        }

        return $this->customerRepository->save($customer);
    }
}
