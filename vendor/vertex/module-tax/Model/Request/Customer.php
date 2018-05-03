<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface as CustomerGroupManagement;
use Magento\Customer\Api\GroupRepositoryInterface as CustomerGroupRepository;
use Magento\Quote\Model\Quote\Address;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;
use Vertex\Tax\Model\Request\Address as AddressFormatter;

/**
 * Customer data formatter for Vertex API Calls
 */
class Customer
{
    /** @var Config */
    private $config;

    /** @var AddressFormatter */
    private $AddressFormatter;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var CustomerGroupRepository */
    private $customerGroupRepository;

    /** @var CustomerGroupManagement */
    private $customerGroupManagement;

    /** @var TaxClassNameRepository */
    private $taxClassNameRepository;

    /**
     * @param Config $config
     * @param AddressFormatter $AddressFormatter
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerGroupRepository $customerGroupRepository
     * @param CustomerGroupManagement $customerGroupManagement
     * @param TaxClassNameRepository $taxClassNameRepository
     */
    public function __construct(
        Config $config,
        AddressFormatter $AddressFormatter,
        CustomerRepositoryInterface $customerRepository,
        CustomerGroupRepository $customerGroupRepository,
        CustomerGroupManagement $customerGroupManagement,
        TaxClassNameRepository $taxClassNameRepository
    ) {
        $this->AddressFormatter = $AddressFormatter;
        $this->config = $config;
        $this->customerRepository = $customerRepository;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerGroupManagement = $customerGroupManagement;
        $this->taxClassNameRepository = $taxClassNameRepository;
    }

    /**
     * Create a properly formatted array of Customer Data for a Vertex API
     *
     * @param Address $taxAddress
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedCustomerData(Address $taxAddress)
    {
        $data = [];
        $street = $taxAddress->getStreet();

        $customerId = $taxAddress->getCustomerId();

        $address = $this->AddressFormatter->getFormattedAddressData(
            $street,
            $taxAddress->getCity(),
            $taxAddress->getRegionId(),
            $taxAddress->getPostcode(),
            $taxAddress->getCountryId()
        );

        $data['CustomerCode']['_'] = $this->getCustomerCodeById($customerId);

        if ($customerId) {
            $customerData = $this->customerRepository->getById($customerId);
            $customerTaxClass = $this->customerGroupRepository->getById($customerData->getGroupId())->getTaxClassId();
        } else {
            $customerTaxClass = $this->customerGroupManagement->getNotLoggedInGroup()->getTaxClassId();
        }

        $data['CustomerCode']['classCode'] = $this->taxClassNameRepository->getById($customerTaxClass);
        $data['Destination'] = $address;

        return $data;
    }

    /**
     * Retrieve a Customer's Custom Code given their ID
     *
     * @param int $customerId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerCodeById($customerId = 0)
    {
        $customerCode = '';
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);

            if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getVertexCustomerCode()) {
                $customerCode = $customer->getExtensionAttributes()->getVertexCustomerCode();
            }
        }

        if (empty($customerCode)) {
            $customerCode = $this->config->getDefaultCustomerCode();
        }

        return $customerCode;
    }

    /**
     * Retrieve the name of the Tax Class attached to a Customer Group
     *
     * @param int $groupId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function taxClassNameByCustomerGroupId($groupId)
    {
        $classId = $this->customerGroupRepository->getById($groupId)->getTaxClassId();
        return $this->taxClassNameRepository->getById($classId);
    }
}
