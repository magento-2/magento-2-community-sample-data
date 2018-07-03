<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\CollectionPoint;

/**
 * Temando Order Collection Point Interface – Order Details/Fulfillment
 *
 * @api
 * @package Temando\Shipping\Api
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
interface OrderCollectionPointInterface
{
    const RECIPIENT_ADDRESS_ID = 'recipient_address_id';
    const COLLECTION_POINT_ID = 'collection_point_id';
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
    public function getCollectionPointId();

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
