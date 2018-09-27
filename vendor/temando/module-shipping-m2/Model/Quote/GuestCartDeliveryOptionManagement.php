<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\GuestCart\GuestShippingAddressManagementInterface;
use Temando\Shipping\Api\Quote\GuestCartDeliveryOptionManagementInterface;

/**
 * Manage delivery options for guest's.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class GuestCartDeliveryOptionManagement implements GuestCartDeliveryOptionManagementInterface
{
    /**
     * @var GuestShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var DeliveryOptionManagement
     */
    private $deliveryOptionManagement;

    /**
     * GuestCartDeliveryOptionManagement constructor.
     *
     * @param DeliveryOptionManagement $deliveryOptionManagement
     * @param GuestShippingAddressManagementInterface $addressManagement
     */
    public function __construct(
        DeliveryOptionManagement $deliveryOptionManagement,
        GuestShippingAddressManagementInterface $addressManagement
    ) {
        $this->deliveryOptionManagement = $deliveryOptionManagement;
        $this->addressManagement = $addressManagement;
    }

    /**
     * Handle selected delivery option.
     *
     * @param string $cartId The shopping cart ID.
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
