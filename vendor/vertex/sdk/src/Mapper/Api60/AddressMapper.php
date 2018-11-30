<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Data\Address;
use Vertex\Data\AddressInterface;
use Vertex\Mapper\AddressMapperInterface;
use Vertex\Mapper\MapperUtilities;

/**
 * API Level 60 implementation of {@see AddressMapperInterface}
 */
class AddressMapper implements AddressMapperInterface
{
    /**
     * Maximum amount of characters allowed for City
     */
    const CITY_MAX = MapperUtilities::DEFAULT_MAX;

    /**
     * Minimum amount of characters allowed for City
     */
    const CITY_MIN = MapperUtilities::DEFAULT_MIN;

    /**
     * Maximum amount of characters allowed for Country
     */
    const COUNTRY_MAX = MapperUtilities::DEFAULT_MAX;

    /**
     * Minimum amount of characters allowed for Country
     */
    const COUNTRY_MIN = MapperUtilities::DEFAULT_MIN;

    /**
     * Maximum amount of characters allowed for Main Division
     */
    const MAIN_DIVISION_MAX = MapperUtilities::DEFAULT_MAX;

    /**
     * Minimum amount of characters allowed for Main Division
     */
    const MAIN_DIVISION_MIN = MapperUtilities::DEFAULT_MIN;

    /**
     * Maximum amount of characters allowed for Postal Code
     */
    const POSTAL_CODE_MAX = 15;

    /**
     * Minimum amount of characters allowed for Postal Code
     */
    const POSTAL_CODE_MIN = MapperUtilities::DEFAULT_MIN;

    /**
     * Maximum amount of characters allowed for Street Address
     */
    const STREET_ADDRESS_MAX = 100;

    /**
     * Minimum amount of characters allowed for Street Address
     */
    const STREET_ADDRESS_MIN = MapperUtilities::DEFAULT_MIN;

    /**
     * Maximum amount of characters allowed for Sub Division
     */
    const SUB_DIVISION_MAX = MapperUtilities::DEFAULT_MAX;

    /**
     * Minimum amount of characters allowed for Sub Division
     */
    const SUB_DIVISION_MIN = MapperUtilities::DEFAULT_MIN;

    /** @var MapperUtilities */
    private $utilities;

    /**
     * @param MapperUtilities|null $utilities
     */
    public function __construct(MapperUtilities $utilities = null)
    {
        $this->utilities = $utilities ?: new MapperUtilities();
    }

    /**
     * @inheritdoc
     */
    public function build(\stdClass $map)
    {
        $address = new Address();
        $street = [];
        if (isset($map->StreetAddress1)) {
            $street[] = (string)$map->StreetAddress1;
        }
        if (isset($map->StreetAddress2)) {
            $street[] = (string)$map->StreetAddress2;
        }
        $address->setStreetAddress($street);

        if (isset($map->City)) {
            $address->setCity((string)$map->City);
        }

        if (isset($map->MainDivision)) {
            $address->setMainDivision((string)$map->MainDivision);
        }

        if (isset($map->SubDivision)) {
            $address->setSubDivision((string)$map->SubDivision);
        }

        if (isset($map->PostalCode)) {
            $address->setPostalCode((string)$map->PostalCode);
        }

        if (isset($map->Country)) {
            $address->setCountry((string)$map->Country);
        }

        return $address;
    }

    /**
     * @inheritdoc
     */
    public function map(AddressInterface $object)
    {
        $map = new \stdClass();
        $map = $this->addStreetAddressToMap($object->getStreetAddress(), $map);
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getCity(),
            'City',
            static::CITY_MIN,
            static::CITY_MAX,
            true
        );
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getMainDivision(),
            'MainDivision',
            static::MAIN_DIVISION_MIN,
            static::MAIN_DIVISION_MAX,
            true,
            'Main Division'
        );
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getSubDivision(),
            'SubDivision',
            static::SUB_DIVISION_MIN,
            static::SUB_DIVISION_MAX,
            true,
            'Sub Division'
        );
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getPostalCode(),
            'PostalCode',
            static::POSTAL_CODE_MIN,
            static::POSTAL_CODE_MAX,
            true,
            'Postal Code'
        );
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getCountry(),
            'Country',
            static::COUNTRY_MIN,
            static::COUNTRY_MAX,
            true
        );

        return $map;
    }

    /**
     * Validate and, if valid, add Street Address to the mapping
     *
     * @param string[] $streetAddress
     * @param \stdClass $mapping
     * @return \stdClass
     * @throws \Vertex\Exception\ValidationException
     */
    private function addStreetAddressToMap(array $streetAddress, \stdClass $mapping)
    {
        if (empty($streetAddress)) {
            return $mapping;
        }

        $line = [1 => null, 2 => null];
        if (count($streetAddress) > 0) {
            $line[1] = reset($streetAddress);
        }
        if (count($streetAddress) > 1) {
            $line[2] = next($streetAddress);
        }

        foreach ($line as $key => $streetLine) {
            $mapping = $this->utilities->addToMapWithLengthValidation(
                $mapping,
                $streetLine,
                "StreetAddress{$key}",
                static::STREET_ADDRESS_MIN,
                static::STREET_ADDRESS_MAX,
                true,
                "Street Address Line {$key}"
            );
        }

        return $mapping;
    }
}
