<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\Model\AbstractModel;
use Temando\Shipping\Api\Data\Delivery\OrderPickupLocationInterface;
use Temando\Shipping\Model\ResourceModel\Delivery\OrderPickupLocation as PickupLocationResource;

/**
 * Temando Order Pickup Location Entity
 *
 * This model contains a subset of data that is used in the shipping module.
 * It does not contain all data as available in its platform representation.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderPickupLocation extends AbstractModel implements OrderPickupLocationInterface
{
    /**
     * Init resource model.
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(PickupLocationResource::class);
    }

    /**
     * @return int
     */
    public function getRecipientAddressId()
    {
        return $this->getData(OrderPickupLocationInterface::RECIPIENT_ADDRESS_ID);
    }

    /**
     * @return string
     */
    public function getPickupLocationId()
    {
        return $this->getData(OrderPickupLocationInterface::PICKUP_LOCATION_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(OrderPickupLocationInterface::NAME);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(OrderPickupLocationInterface::COUNTRY);
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(OrderPickupLocationInterface::REGION);
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(OrderPickupLocationInterface::POSTCODE);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getData(OrderPickupLocationInterface::CITY);
    }

    /**
     * @return string[]
     */
    public function getStreet()
    {
        return $this->getData(OrderPickupLocationInterface::STREET);
    }
}
