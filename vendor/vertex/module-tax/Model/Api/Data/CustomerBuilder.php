<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\GroupManagementInterface as CustomerGroupManagement;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Vertex\Data\CustomerInterface;
use Vertex\Data\CustomerInterfaceFactory;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Builds a Customer object for use with the Vertex SDK
 */
class CustomerBuilder
{
    /** @var AddressBuilder */
    private $addressBuilder;

    /** @var Config */
    private $config;

    /** @var CustomerInterfaceFactory */
    private $customerFactory;

    /** @var CustomerGroupManagement */
    private $customerGroupManagement;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var GroupRepositoryInterface */
    private $groupRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var TaxClassNameRepository */
    private $taxClassNameRepository;

    /**
     * @param Config $config
     * @param AddressBuilder $addressBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerGroupManagement $customerGroupManagement
     * @param TaxClassNameRepository $taxClassNameRepository
     * @param CustomerInterfaceFactory $customerFactory
     * @param LoggerInterface $logger
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        Config $config,
        AddressBuilder $addressBuilder,
        CustomerRepositoryInterface $customerRepository,
        CustomerGroupManagement $customerGroupManagement,
        TaxClassNameRepository $taxClassNameRepository,
        CustomerInterfaceFactory $customerFactory,
        LoggerInterface $logger,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->addressBuilder = $addressBuilder;
        $this->config = $config;
        $this->customerRepository = $customerRepository;
        $this->customerGroupManagement = $customerGroupManagement;
        $this->taxClassNameRepository = $taxClassNameRepository;
        $this->customerFactory = $customerFactory;
        $this->logger = $logger;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Create a properly formatted array of Customer Data for a Vertex API
     *
     * @param AddressInterface $taxAddress
     * @param int|null $customerId
     * @param int|null $taxClassId
     * @param string|null $storeCode
     * @return CustomerInterface
     */
    public function buildFromCustomerAddress(
        AddressInterface $taxAddress = null,
        $customerId = null,
        $taxClassId = null,
        $storeCode = null
    ) {
        return $this->buildFromAddress($taxAddress, $customerId, $taxClassId, $storeCode);
    }

    /**
     * Create a {@see CustomerInterface} from an {@see Order}
     *
     * @param Order $order
     * @return CustomerInterface
     */
    public function buildFromOrder(Order $order)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->create();
        $customer->setTaxClass($this->getCustomerClassById($order->getCustomerId()));
        $customer->setCode($this->getCustomerCodeById($order->getCustomerId()));

        $orderAddress = $order->getIsVirtual() ? $order->getBillingAddress() : $order->getShippingAddress();

        $address = $this->addressBuilder
            ->setStreet($orderAddress->getStreet())
            ->setCity($orderAddress->getCity())
            ->setRegionId($orderAddress->getRegionId())
            ->setPostalCode($orderAddress->getPostcode())
            ->setCountryCode($orderAddress->getCountryId())
            ->build();

        $customer->setDestination($address);

        return $customer;
    }

    /**
     * Create a properly formatted array of Customer Data for a Vertex API
     *
     * @param OrderAddressInterface $taxAddress
     * @param int|null $customerId
     * @param int|null $customerGroupId
     * @param string|null $storeCode
     * @return CustomerInterface
     */
    public function buildFromOrderAddress(
        OrderAddressInterface $taxAddress = null,
        $customerId = null,
        $customerGroupId = null,
        $storeCode = null
    ) {
        try {
            $group = $customerGroupId ? $this->groupRepository->getById($customerGroupId) : null;
        } catch (\Exception $e) {
            $group = null;
        }
        $taxClassId = $group ? $group->getTaxClassId() : null;
        return $this->buildFromAddress($taxAddress, $customerId, $taxClassId, $storeCode);
    }

    /**
     * Create a properly formatted array of Customer Data for the Vertex API
     *
     * This method exists to build addresses based off any number of Magento's
     * Address interfaces.
     *
     * @param AddressInterface|OrderAddressInterface|null $taxAddress
     * @param int $customerId
     * @param int $taxClassId
     * @param string $storeCode
     * @return CustomerInterface
     */
    private function buildFromAddress($taxAddress = null, $customerId = null, $taxClassId = null, $storeCode = null)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->create();

        if ($taxAddress) {
            $address = $this->addressBuilder
                ->setStreet($taxAddress->getStreet())
                ->setCity($taxAddress->getCity())
                ->setRegionId($taxAddress->getRegionId())
                ->setPostalCode($taxAddress->getPostcode())
                ->setCountryCode($taxAddress->getCountryId())
                ->build();

            $customer->setDestination($address);
        }

        $customer->setCode($this->getCustomerCodeById($customerId, $storeCode));

        $class = $taxClassId
            ? $this->taxClassNameRepository->getById($taxClassId)
            : $this->getCustomerClassById($customerId);

        $customer->setTaxClass($class);

        return $customer;
    }

    /**
     * Retrieve a Customer's Tax Class given their ID
     *
     * @param int $customerId
     * @return string
     */
    private function getCustomerClassById($customerId = 0)
    {
        $customerGroupId = 0;
        $taxClassId = 0;
        try {
            if ($customerId) {
                $customerData = $this->customerRepository->getById($customerId);
                $customerGroupId = $customerData->getGroupId();
            } else {
                $taxClassId = $this->customerGroupManagement->getNotLoggedInGroup()->getTaxClassId();
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }

        return $customerGroupId
            ? $this->taxClassNameRepository->getByCustomerGroupId($customerGroupId)
            : $this->taxClassNameRepository->getById($taxClassId);
    }

    /**
     * Retrieve a Customer's Custom Code given their ID
     *
     * @param int $customerId
     * @param string|null $store
     * @return string|null
     */
    private function getCustomerCodeById($customerId = 0, $store = null)
    {
        if ($customerId === 0 || $customerId === null) {
            return $this->config->getDefaultCustomerCode($store);
        }

        $customerCode = null;
        try {
            $customer = $this->customerRepository->getById($customerId);
            $extensions = $customer->getExtensionAttributes();
            if ($extensions !== null && $extensions->getVertexCustomerCode()) {
                $customerCode = $extensions->getVertexCustomerCode();
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }

        return $customerCode ?: $this->config->getDefaultCustomerCode($store);
    }
}
