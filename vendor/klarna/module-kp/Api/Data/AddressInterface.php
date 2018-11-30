<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api\Data;

/**
 * Interface AddressInterface
 *
 * @package Klarna\Kp\Api\Data
 */
interface AddressInterface extends ApiObjectInterface
{
    /**
     * Title. Possible values Mr or Mrs
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Given name. [REQUIRED field]
     *
     * @param string $given_name
     */
    public function setGivenName($given_name);

    /**
     * Family name. [REQUIRED field]
     *
     * @param string $family_name
     */
    public function setFamilyName($family_name);

    /**
     * E+mail address. [REQUIRED field]
     *
     * @param string $email
     */
    public function setEmail($email);

    /**
     * Phone number.
     *
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * Street address, first line. [REQUIRED field]
     *
     * @param string $street_address
     */
    public function setStreetAddress($street_address);

    /**
     * Street address, second line.
     *
     * @param string $street_address
     */
    public function setStreetAddress2($street_address);

    /**
     * City. [REQUIRED field]
     *
     * @param string $city
     */
    public function setCity($city);

    /**
     * Region
     *
     * @param string $region
     */
    public function setRegion($region);

    /**
     * Postal/post code. [REQUIRED field]
     *
     * @param string $postal_code
     */
    public function setPostalCode($postal_code);

    /**
     * ISO 3166 alpha+2: Country. [REQUIRED field]
     *
     * @param string $country
     */
    public function setCountry($country);
}
