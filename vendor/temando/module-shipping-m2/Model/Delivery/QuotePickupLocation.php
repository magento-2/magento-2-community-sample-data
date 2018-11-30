<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\Model\AbstractModel;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Model\ResourceModel\Delivery\QuotePickupLocation as PickupLocationResource;

/**
 * Temando Quote Pickup Location Entity
 *
 * This model contains a subset of data that is used in the shipping module.
 * It does not contain all data as available in its platform representation.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class QuotePickupLocation extends AbstractModel implements QuotePickupLocationInterface
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
    public function getEntityId()
    {
        return $this->getData(QuotePickupLocationInterface::ENTITY_ID);
    }

    /**
     * @return string
     */
    public function getPickupLocationId()
    {
        return $this->getData(QuotePickupLocationInterface::PICKUP_LOCATION_ID);
    }

    /**
     * @return int
     */
    public function getRecipientAddressId()
    {
        return $this->getData(QuotePickupLocationInterface::RECIPIENT_ADDRESS_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(QuotePickupLocationInterface::NAME);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(QuotePickupLocationInterface::COUNTRY);
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(QuotePickupLocationInterface::REGION);
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(QuotePickupLocationInterface::POSTCODE);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getData(QuotePickupLocationInterface::CITY);
    }

    /**
     * @return string[]
     */
    public function getStreet()
    {
        return $this->getData(QuotePickupLocationInterface::STREET);
    }

    /**
     * @return string[][]
     */
    public function getOpeningHours()
    {
        return $this->getData(QuotePickupLocationInterface::OPENING_HOURS);
    }

    /**
     * @return string[][]
     */
    public function getShippingExperiences()
    {
        return $this->getData(QuotePickupLocationInterface::SHIPPING_EXPERIENCES);
    }

    /**
     * @return bool
     */
    public function isSelected()
    {
        return $this->getData(QuotePickupLocationInterface::SELECTED);
    }
}
