<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Psr\Log\LoggerInterface;

/**
 * Determines the address to use for tax calculation
 */
class AddressDeterminer
{
    /** @var AddressRepositoryInterface */
    private $addressRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRepositoryInterface $addressRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->logger = $logger;
    }

    /**
     * Determine the address to use for Tax Calculation
     *
     * @param AddressInterface|QuoteAddressInterface $address
     * @param int|null $customerId
     * @param bool $virtual Whether or not the cart or order is virtual
     * @return AddressInterface|null
     */
    public function determineAddress($address = null, $customerId = null, $virtual = false)
    {
        if ($address !== null && !($address instanceof AddressInterface || $address instanceof QuoteAddressInterface)) {
            throw new \InvalidArgumentException(
                '$address must be a Customer or Quote Address.  Is: '
                . (is_object($address) ? get_class($address) : gettype($address))
            );
        }

        if (!$customerId || ($address !== null && $address->getCountryId() !== null)) {
            return $address;
        }

        // Default to billing address for virtual orders unless there is not one
        if ($virtual) {
            $billing = $this->getDefaultBilling($customerId);
            return $billing !== null ? $billing : $this->getDefaultShipping($customerId);
        }

        // Default to shipping address for physical orders unless there is not one
        $shipping = $this->getDefaultShipping($customerId);
        return $shipping !== null ? $shipping : $this->getDefaultBilling($customerId);
    }

    /**
     * Retrieve the default billing address for a customer
     *
     * @param int $customerId
     * @return AddressInterface|null
     */
    private function getDefaultBilling($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $addressId = $customer->getDefaultBilling();

            return $this->addressRepository->getById($addressId);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Retrieve the default shipping address for a customer
     *
     * @param int $customerId
     * @return AddressInterface|null
     */
    private function getDefaultShipping($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $addressId = $customer->getDefaultShipping();

            return $this->addressRepository->getById($addressId);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return null;
        }
    }
}
