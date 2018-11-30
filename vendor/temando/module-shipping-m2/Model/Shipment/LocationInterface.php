<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Shipment Location Interface.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface LocationInterface
{
    const COMPANY = 'company';
    const PERSON_FIRST_NAME = 'person_first_name';
    const PERSON_LAST_NAME = 'person_last_name';
    const EMAIL = 'email';
    const PHONE_NUMBER = 'phone_number';
    const STREET = 'street';
    const CITY = 'city';
    const POSTAL_CODE = 'postal_code';
    const REGION_CODE = 'region_code';
    const COUNTRY_CODE = 'country_code';
    const TYPE = 'type';

    /**
     * Get organisation name.
     *
     * @return string
     */
    public function getCompany();

    /**
     * Get contact person's first name.
     *
     * @return string
     */
    public function getPersonFirstName();

    /**
     * Get contact person's last name.
     *
     * @return string
     */
    public function getPersonLastName();

    /**
     * Get contact person's email address.
     * @return string
     */
    public function getEmail();

    /**
     * Get contact person's telephone number.
     *
     * @return string
     */
    public function getPhoneNumber();

    /**
     * Get address street lines.
     *
     * @return string[]
     */
    public function getStreet();

    /**
     * Get address locality.
     *
     * @return string
     */
    public function getCity();

    /**
     * Get address postal code.
     *
     * @return string
     */
    public function getPostalCode();

    /**
     * Get address administrative area code.
     *
     * @return string
     */
    public function getRegionCode();

    /**
     * Get address country ISO 2 code.
     * @return string
     */
    public function getCountryCode();

    /**
     * Get address type.
     * @return string
     */
    public function getType();
}
