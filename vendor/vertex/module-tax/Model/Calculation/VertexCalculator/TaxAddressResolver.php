<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Calculation\VertexCalculator;

use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Resolves a taxable address candidate for use with a TaxQuoteRequest.
 *
 * This class is designed to be a replacement for {@see Magento\Tax\Model\Calculation::getRateRequest}. Since the core
 * rate request generator does not include some line-item specifics needed for accurate rate calculation, this resolver
 * prepares those additional details.
 */
class TaxAddressResolver
{
    /** @var AccountManagementInterface */
    private $customerAccountManagement;

    /**
     * @param AccountManagementInterface $customerAccountManagement
     */
    public function __construct(AccountManagementInterface $customerAccountManagement)
    {
        $this->customerAccountManagement = $customerAccountManagement;
    }

    /**
     * Retrieve a taxable address.
     *
     * @param QuoteAddressInterface|null $billingAddress
     * @param QuoteAddressInterface|null $shippingAddress
     * @param bool $isVirtual
     * @param int|null $customerId
     * @return QuoteAddressInterface
     */
    public function resolve(
        QuoteAddressInterface $billingAddress = null,
        QuoteAddressInterface $shippingAddress = null,
        $isVirtual = false,
        $customerId = null
    ) {
        /**
         * Vertex requires that we base tax on the billing address for virtual orders, ignoring core behavior.
         * @see \Magento\Tax\Model\Config::CONFIG_XML_PATH_BASED_ON
         */
        $address = $isVirtual ? $billingAddress: $shippingAddress;

        if ($address !== null && !$this->validate($address)) {
            $address = $isVirtual
                ? $this->getDefaultBillingAddress($address, $customerId)
                : $this->getDefaultShippingAddress($address, $customerId);
        }

        return $address;
    }

    /**
     * Prepare a new address clone from a given original.
     *
     * Clones are used to populate a given address with missing details without affecting the original. We do not want
     * to affect the original in any way. The clone acts as our proprietary "rate request" object.
     *
     * @param QuoteAddressInterface $originalAddress
     * @param QuoteAddressInterface|QuoteAddressInterface|null $newAddress
     * @return QuoteAddressInterface
     */
    private function cloneAddress(QuoteAddressInterface $originalAddress, $newAddress = null)
    {
        if ($newAddress === null) {
            return $originalAddress;
        }

        $clone = clone $originalAddress;
        $data = [
            'id' => $originalAddress->getId(),
            'country_id' => $newAddress->getCountryId(),
            'region_id' => $newAddress->getRegionId(),
            'region' => $newAddress->getRegion(),
            'postcode' => $newAddress->getPostcode(),
        ];

        $this->setAddressData($clone, $data);

        return $clone;
    }

    /**
     * Retrieve the default customer billing address.
     *
     * @param QuoteAddressInterface $address
     * @param int|null $customerId
     * @return QuoteAddressInterface
     */
    private function getDefaultBillingAddress(QuoteAddressInterface $address, $customerId = null)
    {
        try {
            return $this->cloneAddress(
                $address,
                $this->customerAccountManagement->getDefaultBillingAddress($customerId)
            );
        } catch (NoSuchEntityException $error) {
            return $this->cloneAddress($address);
        } catch (LocalizedException $error) {
            return $this->cloneAddress($address);
        }
    }

    /**
     * Retrieve the default customer shipping address.
     *
     * @param QuoteAddressInterface $address
     * @param int|null $customerId
     * @return QuoteAddressInterface
     */
    private function getDefaultShippingAddress(QuoteAddressInterface $address, $customerId = null)
    {
        try {
            return $this->cloneAddress(
                $address,
                $this->customerAccountManagement->getDefaultShippingAddress($customerId)
            );
        } catch (NoSuchEntityException $error) {
            return $this->cloneAddress($address);
        } catch (LocalizedException $error) {
            return $this->cloneAddress($address);
        }
    }

    /**
     * Write the data set to the given address.
     *
     * @param QuoteAddressInterface $address
     * @param array $data
     * @return void
     */
    private function setAddressData(QuoteAddressInterface $address, array $data = [])
    {
        foreach ($data as $key => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

            if (method_exists($address, $method)) {
                $address->{$method}($value);
            }
        }
    }

    /**
     * Determine whether an address can be used for tax calculation.
     *
     * @param QuoteAddressInterface|null $address
     * @return bool
     */
    private function validate(QuoteAddressInterface $address = null)
    {
        return $address !== null
            && $address->getCountryId()
            && (
                $address->getRegionId()
                || ($address->getRegion() instanceof RegionInterface && $address->getRegion()->getRegionId())
            )
            && $address->getPostcode();
    }
}
