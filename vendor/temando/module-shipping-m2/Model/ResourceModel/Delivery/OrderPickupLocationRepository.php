<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Delivery;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\Delivery\OrderPickupLocationInterface;
use Temando\Shipping\Api\Data\Delivery\OrderPickupLocationInterfaceFactory;
use Temando\Shipping\Model\Delivery\OrderPickupLocation;
use Temando\Shipping\Model\ResourceModel\Delivery\OrderPickupLocation as PickupLocationResource;
use Temando\Shipping\Model\ResourceModel\Repository\OrderPickupLocationRepositoryInterface;

/**
 * Temando Order Pickup Location Repository
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderPickupLocationRepository implements OrderPickupLocationRepositoryInterface
{
    /**
     * @var PickupLocationResource
     */
    private $resource;

    /**
     * @var OrderPickupLocationInterfaceFactory
     */
    private $pickupLocationFactory;

    /**
     * OrderPickupLocationRepository constructor.
     * @param PickupLocationResource $resource
     * @param OrderPickupLocationInterfaceFactory $pickupLocationFactory
     */
    public function __construct(
        PickupLocationResource $resource,
        OrderPickupLocationInterfaceFactory $pickupLocationFactory
    ) {
        $this->resource = $resource;
        $this->pickupLocationFactory = $pickupLocationFactory;
    }

    /**
     * Load pickup location by shipping address id.
     *
     * @param int $addressId
     * @return OrderPickupLocationInterface
     * @throws NoSuchEntityException
     */
    public function get($addressId)
    {
        /** @var OrderPickupLocation $pickupLocation */
        $pickupLocation = $this->pickupLocationFactory->create();

        try {
            $this->resource->load($pickupLocation, $addressId);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Pickup location with id "%1" does not exist.', $addressId));
        }

        if (!$pickupLocation->getRecipientAddressId()) {
            throw new NoSuchEntityException(__('Pickup location with id "%1" does not exist.', $addressId));
        }

        return $pickupLocation;
    }

    /**
     * @param OrderPickupLocationInterface $pickupLocation
     * @return OrderPickupLocationInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderPickupLocationInterface $pickupLocation)
    {
        try {
            /** @var OrderPickupLocation $pickupLocation */
            $this->resource->save($pickupLocation);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save pickup location.'), $exception);
        }

        return $pickupLocation;
    }
}
