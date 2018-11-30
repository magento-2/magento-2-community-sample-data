<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Default implementation of {@see AddressInterface}
 */
class Address implements AddressInterface
{
    /** @var string */
    private $city;

    /** @var string Three character country code */
    private $countryCode;

    /** @var string */
    private $mainDivision;

    /** @var string */
    private $postalCode;

    /** @var string[] */
    private $streetAddress = [];

    /** @var string */
    private $subDivision;

    /**
     * @inheritdoc
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @inheritdoc
     */
    public function getCountry()
    {
        return $this->countryCode;
    }

    /**
     * @inheritdoc
     */
    public function getMainDivision()
    {
        return $this->mainDivision;
    }

    /**
     * @inheritdoc
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @inheritdoc
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * @inheritdoc
     */
    public function getSubDivision()
    {
        return $this->subDivision;
    }

    /**
     * @inheritdoc
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCountry($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setMainDivision($mainDivision)
    {
        $this->mainDivision = $mainDivision;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setStreetAddress(array $streetAddress)
    {
        $this->streetAddress = $streetAddress;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSubDivision($subDivision)
    {
        $this->subDivision = $subDivision;
        return $this;
    }
}
