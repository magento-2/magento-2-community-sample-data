<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Quote;

use Magento\Quote\Api\Data\AddressExtensionInterface;

/**
 * @deprecated since 1.2.0
 * Interface ShippingMethodManagementInterface
 *
 * Allow additional data (extension attributes) to be passed into the shipping
 * estimation process.
 * @api
 */
interface ShippingMethodManagementInterface
{
    /**
     * Estimate shipping with extension attributes
     *
     * @see \Magento\Quote\Api\ShippingMethodManagementInterface::estimateByAddressId
     *
     * @param int $cartId The shopping cart ID.
     * @param int $addressId The estimate address id
     * @param \Magento\Quote\Api\Data\AddressExtensionInterface|null $extensionAttributes
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     */
    public function estimateByAddressId($cartId, $addressId, AddressExtensionInterface $extensionAttributes = null);
}
