<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Api\Request;

use Klarna\Kp\Api\Data\AddressInterface;

/**
 * Class Address
 *
 * @package Klarna\Kp\Model\Api\Request
 */
class Address implements AddressInterface
{
    use \Klarna\Kp\Model\Api\Export;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $given_name;

    /**
     * @var string
     */
    private $family_name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $street_address;

    /**
     * @var string
     */
    private $street_address2;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $region;

    /**
     * @var string
     */
    private $postal_code;

    /**
     * @var string
     */
    private $country;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->exports[] = $key;
            }
        }
    }

    /**
     * Title. Possible values Mr or Mrs
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Given name. [REQUIRED field]
     *
     * @param string $given_name
     */
    public function setGivenName($given_name)
    {
        $this->given_name = $given_name;
    }

    /**
     * Family name. [REQUIRED field]
     *
     * @param string $family_name
     */
    public function setFamilyName($family_name)
    {
        $this->family_name = $family_name;
    }

    /**
     * E+mail address. [REQUIRED field]
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Phone number.
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Street address, first line. [REQUIRED field]
     *
     * @param string $street_address
     */
    public function setStreetAddress($street_address)
    {
        $this->street_address = $street_address;
    }

    /**
     * Street address, second line.
     *
     * @param string $street_address
     */
    public function setStreetAddress2($street_address)
    {
        $this->street_address2 = $street_address;
    }

    /**
     * City. [REQUIRED field]
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Region
     *
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * Postal/post code. [REQUIRED field]
     *
     * @param string $postal_code
     */
    public function setPostalCode($postal_code)
    {
        $this->postal_code = $postal_code;
    }

    /**
     * ISO 3166 alpha+2: Country. [REQUIRED field]
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }
}
