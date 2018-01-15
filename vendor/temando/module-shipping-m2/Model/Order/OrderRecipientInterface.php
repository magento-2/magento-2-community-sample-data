<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

/**
 * Temando Order Recipient Interface
 *
 * An order recipient as associated with an order entity at the Temando platform.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface OrderRecipientInterface
{
    const COMPANY = 'company';
    const LASTNAME = 'lastname';
    const FIRSTNAME = 'firstname';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const FAX = 'fax';
    const NATIONAL_ID = 'national_id';
    const TAX_ID = 'tax_id';
    const STREET = 'street';
    const COUNTRY_CODE = 'country_code';
    const REGION = 'region';
    const POSTAL_CODE = 'postal_code';
    const CITY = 'city';
    const SUBURB = 'suburb';
    const LONGITUDE = 'longitude';
    const LATITUDE = 'latitude';

    /**
     * @return string
     */
    public function getCompany();

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @return string
     */
    public function getFax();

    /**
     * @return string
     */
    public function getNationalId();

    /**
     * @return string
     */
    public function getTaxId();

    /**
     * @return string[]
     */
    public function getStreet();

    /**
     * @return string
     */
    public function getCountryCode();

    /**
     * @return string
     */
    public function getRegion();

    /**
     * @return string
     */
    public function getPostalCode();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getSuburb();

    /**
     * @return float
     */
    public function getLongitude();

    /**
     * @return float
     */
    public function getLatitude();
}
