<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Quote;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Temando\Shipping\Model\Delivery\CollectionPointManagement;
use Temando\Shipping\Model\Delivery\PickupLocationManagement;
use Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface;

/**
 * Manage delivery options.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class DeliveryOptionManagement
{
    /**
     * Ship to regular address
     */
    const DELIVERY_OPTION_ADDRESS = 'toAddress';

    /**
     * Ship to collection point
     */
    const DELIVERY_OPTION_COLLECTION_POINT = 'toCollectionPoint';

    /**
     * Collect at pickup location (Click & Collect)
     */
    const DELIVERY_OPTION_PICKUP = 'clickAndCollect';

    /**
     * Shipping does not apply, e.g. virtual orders
     */
    const DELIVERY_OPTION_NONE = 'noShipping';

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var CollectionPointManagement
     */
    private $collectionPointManagement;

    /**
     * @var PickupLocationManagement
     */
    private $pickupLocationManagement;

    /**
     * DeliveryOptionManagement constructor.
     *
     * @param AddressRepositoryInterface $addressRepository
     * @param CollectionPointManagement $collectionPointManagement
     * @param PickupLocationManagement $pickupLocationManagement
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        CollectionPointManagement $collectionPointManagement,
        PickupLocationManagement $pickupLocationManagement
    ) {
        $this->addressRepository = $addressRepository;
        $this->collectionPointManagement = $collectionPointManagement;
        $this->pickupLocationManagement = $pickupLocationManagement;
    }

    /**
     * Perform actions when a consumer changes the delivery option.
     * - Clean up previously selected option
     * - Set current option pending (i.e. option chosen, no details selected yet)
     *
     *
     * @param int $shippingAddressId
     * @param string $selectedOption
     *
     * @return void
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function selectOption($shippingAddressId, $selectedOption)
    {
        switch ($selectedOption) {
            case self::DELIVERY_OPTION_ADDRESS:
                $this->collectionPointManagement->deleteSearchRequest($shippingAddressId);
                $this->pickupLocationManagement->deleteSearchRequest($shippingAddressId);
                break;

            case self::DELIVERY_OPTION_COLLECTION_POINT:
                $this->addressRepository->deleteByShippingAddressId($shippingAddressId);
                $this->pickupLocationManagement->deleteSearchRequest($shippingAddressId);
                $this->collectionPointManagement->saveSearchRequest($shippingAddressId, '', '', true);
                break;

            case self::DELIVERY_OPTION_PICKUP:
                $this->addressRepository->deleteByShippingAddressId($shippingAddressId);
                $this->collectionPointManagement->deleteSearchRequest($shippingAddressId);
                $this->pickupLocationManagement->saveSearchRequest($shippingAddressId, true);
                break;
        }
    }
}
