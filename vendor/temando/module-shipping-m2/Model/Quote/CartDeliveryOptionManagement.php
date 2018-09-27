<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Temando\Shipping\Api\Quote\CartDeliveryOptionManagementInterface;

/**
 * Manage delivery options for logged in customers.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class CartDeliveryOptionManagement implements CartDeliveryOptionManagementInterface
{
    /**
     * @var ShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var DeliveryOptionManagement
     */
    private $deliveryOptionManagement;

    /**
     * CartDeliveryOptionManagement constructor.
     *
     * @param DeliveryOptionManagement $deliveryOptionManagement
     * @param ShippingAddressManagementInterface $addressManagement
     */
    public function __construct(
        DeliveryOptionManagement $deliveryOptionManagement,
        ShippingAddressManagementInterface $addressManagement
    ) {
        $this->deliveryOptionManagement = $deliveryOptionManagement;
        $this->addressManagement = $addressManagement;
    }

    /**
     * Handle selected delivery option.
     *
     * @param int $cartId The shopping cart ID.
     * @param string $selectedOption
     * @return void
     * @throws LocalizedException
     */
    public function save($cartId, $selectedOption)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        $this->deliveryOptionManagement->selectOption($shippingAddress->getId(), $selectedOption);
    }
}
