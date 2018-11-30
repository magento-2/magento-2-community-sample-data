<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Location;

/**
 * Temando API Location Address Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Address
{
    /**
     * @var bool
     */
    private $isOrganisation;

    /**
     * @var string[]
     */
    private $lines = [];

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $locality;

    /**
     * @var string
     */
    private $dependentLocality;

    /**
     * @var string
     */
    private $administrativeArea;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @return bool
     */
    public function getIsOrganisation()
    {
        return $this->isOrganisation;
    }

    /**
     * @param bool $isOrganisation
     * @return void
     */
    public function setIsOrganisation($isOrganisation)
    {
        $this->isOrganisation = $isOrganisation;
    }

    /**
     * @return string[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param string[] $lines
     * @return void
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     * @return void
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return void
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     * @return void
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getDependentLocality()
    {
        return $this->dependentLocality;
    }

    /**
     * @param string $dependentLocality
     * @return void
     */
    public function setDependentLocality($dependentLocality)
    {
        $this->dependentLocality = $dependentLocality;
    }

    /**
     * @return string
     */
    public function getAdministrativeArea()
    {
        return $this->administrativeArea;
    }

    /**
     * @param string $administrativeArea
     * @return void
     */
    public function setAdministrativeArea($administrativeArea)
    {
        $this->administrativeArea = $administrativeArea;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     * @return void
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     * @return void
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }
}
