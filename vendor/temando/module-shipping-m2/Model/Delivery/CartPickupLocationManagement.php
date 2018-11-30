<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Api\Delivery\CartPickupLocationManagementInterface;

/**
 * Manage Pickup Location Searches
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CartPickupLocationManagement implements CartPickupLocationManagementInterface
{
    /**
     * @var ShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var PickupLocationManagement
     */
    private $pickupLocationManagement;

    /**
     * CartPickupLocationManagement constructor.
     *
     * @param ShippingAddressManagementInterface $addressManagement
     * @param PickupLocationManagement $pickupLocationManagement
     */
    public function __construct(
        ShippingAddressManagementInterface $addressManagement,
        PickupLocationManagement $pickupLocationManagement
    ) {
        $this->addressManagement = $addressManagement;
        $this->pickupLocationManagement = $pickupLocationManagement;
    }

    /**
     * @param int $cartId
     * @return QuotePickupLocationInterface[]
     * @throws NoSuchEntityException
     */
    public function getPickupLocations($cartId)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->pickupLocationManagement->getPickupLocations($shippingAddress->getId());
    }

    /**
     * @param int $cartId
     * @param int $entityId
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function selectPickupLocation($cartId, $entityId)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->pickupLocationManagement->selectPickupLocation($shippingAddress->getId(), $entityId);
    }
}
