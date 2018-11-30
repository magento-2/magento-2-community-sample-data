<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Quote;

use Magento\Quote\Api\Data\AddressExtensionInterface;
use Temando\Shipping\Api\Quote\ShippingMethodManagementInterface;

/**
 * @deprecated since 1.2.0
 * ShippingMethodManagement
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShippingMethodManagement implements ShippingMethodManagementInterface
{
    /**
     * @var \Magento\Quote\Api\ShippingMethodManagementInterface
     */
    private $shippingMethodManagement;

    /**
     * ShippingMethodManagement
     *
     * @param \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManagement
     */
    public function __construct(\Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManagement)
    {
        $this->shippingMethodManagement = $shippingMethodManagement;
    }

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
    public function estimateByAddressId($cartId, $addressId, AddressExtensionInterface $extensionAttributes = null)
    {
        return $this->shippingMethodManagement->estimateByAddressId($cartId, $addressId);
    }
}
