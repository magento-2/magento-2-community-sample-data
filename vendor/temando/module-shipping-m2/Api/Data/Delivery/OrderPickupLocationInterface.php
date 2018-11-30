<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\Delivery;

/**
 * Temando Order Pickup Location Interface – Order Details/Fulfillment
 *
 * @api
 * @package Temando\Shipping\Api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface OrderPickupLocationInterface
{
    const RECIPIENT_ADDRESS_ID = 'recipient_address_id';
    const PICKUP_LOCATION_ID = 'pickup_location_id';
    const NAME = 'name';
    const COUNTRY = 'country';
    const REGION = 'region';
    const POSTCODE = 'postcode';
    const CITY = 'city';
    const STREET = 'street';

    /**
     * @return int
     */
    public function getRecipientAddressId();

    /**
     * @return string
     */
    public function getPickupLocationId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return string
     */
    public function getRegion();

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string[]
     */
    public function getStreet();
}
