<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Represents a physical or mailing address
 *
 * @api
 */
interface AddressInterface
{
    /**
     * Get the proper name of the city
     *
     * @return string|null
     */
    public function getCity();

    /**
     * Get the Country
     *
     * @return string|null ISO 3166-1 Alpha-3 country code
     */
    public function getCountry();

    /**
     * Get the proper name or the postal abbreviation of the state, province, or territory
     *
     * @return string|null
     */
    public function getMainDivision();

    /**
     * Get the Postal Code
     *
     * @return string|null
     */
    public function getPostalCode();

    /**
     * Get the street address
     *
     * @return string[]
     */
    public function getStreetAddress();

    /**
     * Get the name of the county
     *
     * @return string|null
     */
    public function getSubDivision();

    /**
     * Set the proper name of the city
     *
     * @param string $city
     * @return AddressInterface
     */
    public function setCity($city);

    /**
     * Set the Country Code
     *
     * @param string $countryCode ISO 3166-1 Alpha-3 country code
     * @return AddressInterface
     */
    public function setCountry($countryCode);

    /**
     * Set the proper name or the postal abbreviation of the state, province, or territory
     *
     * @param string $region
     * @return AddressInterface
     */
    public function setMainDivision($region);

    /**
     * Set the Postal Code
     *
     * @param string $postalCode
     * @return AddressInterface
     */
    public function setPostalCode($postalCode);

    /**
     * Set the street address
     *
     * @param array $streetAddress
     * @return AddressInterface
     */
    public function setStreetAddress(array $streetAddress);

    /**
     * Set the name of the county
     *
     * @param string $subDivision
     * @return AddressInterface
     */
    public function setSubDivision($subDivision);
}
