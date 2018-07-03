<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location;

/**
 * Temando API Order Included  CollectionPoint Address Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Address
{
    /**
     * @var string[]
     */
    private $lines;

    /**
     * @var string
     */
    private $locality;

    /**
     * @var string
     */
    private $administrativeArea;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @return string[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param string[] $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
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
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
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
     */
    public function setAdministrativeArea($administrativeArea)
    {
        $this->administrativeArea = $administrativeArea;
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
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
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
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
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
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }
}
