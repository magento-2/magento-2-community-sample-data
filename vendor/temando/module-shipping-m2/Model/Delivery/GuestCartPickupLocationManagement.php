<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\GuestCart\GuestShippingAddressManagementInterface;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Api\Delivery\GuestCartPickupLocationManagementInterface;

/**
 * Manage Pickup Location Searches
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GuestCartPickupLocationManagement implements GuestCartPickupLocationManagementInterface
{
    /**
     * @var GuestShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var PickupLocationManagement
     */
    private $pickupLocationManagement;

    /**
     * GuestCartPickupLocationManagement constructor.
     *
     * @param GuestShippingAddressManagementInterface $addressManagement
     * @param PickupLocationManagement $pickupLocationManagement
     */
    public function __construct(
        GuestShippingAddressManagementInterface $addressManagement,
        PickupLocationManagement $pickupLocationManagement
    ) {
        $this->addressManagement = $addressManagement;
        $this->pickupLocationManagement = $pickupLocationManagement;
    }

    /**
     * @param string $cartId
     * @return QuotePickupLocationInterface[]
     * @throws NoSuchEntityException
     */
    public function getPickupLocations($cartId)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->pickupLocationManagement->getPickupLocations($shippingAddress->getId());
    }

    /**
     * @param string $cartId
     * @param int $entityId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function selectPickupLocation($cartId, $entityId)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->pickupLocationManagement->selectPickupLocation($shippingAddress->getId(), $entityId);
    }
}
