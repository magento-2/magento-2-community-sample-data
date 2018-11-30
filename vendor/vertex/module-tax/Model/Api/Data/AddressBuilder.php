<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Directory\Api\Data\CountryInformationInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Vertex\Data\AddressInterface;
use Vertex\Data\AddressInterfaceFactory;
use Vertex\Tax\Model\ZipCodeFixer;

/**
 * Builds an Address object for use with the Vertex SDK
 */
class AddressBuilder
{
    /** @var AddressInterfaceFactory */
    private $addressFactory;

    /** @var string */
    private $city;

    /** @var string */
    private $countryCode;

    /** @var CountryInformationAcquirerInterface */
    private $countryInformationAcquirer;

    /** @var CountryInformationInterfaceFactory */
    private $countryInformationFactory;

    /** @var string */
    private $postalCode;

    /** @var string */
    private $regionId;

    /** @var string[] */
    private $street;

    /** @var ZipCodeFixer */
    private $zipCodeFixer;

    /**
     * @param CountryInformationAcquirerInterface $countryInformationAcquirer
     * @param CountryInformationInterfaceFactory $countryInformationFactory
     * @param ZipCodeFixer $zipCodeFixer
     * @param AddressInterfaceFactory $addressFactory
     */
    public function __construct(
        CountryInformationAcquirerInterface $countryInformationAcquirer,
        CountryInformationInterfaceFactory $countryInformationFactory,
        ZipCodeFixer $zipCodeFixer,
        AddressInterfaceFactory $addressFactory
    ) {
        $this->countryInformationAcquirer = $countryInformationAcquirer;
        $this->countryInformationFactory = $countryInformationFactory;
        $this->zipCodeFixer = $zipCodeFixer;
        $this->addressFactory = $addressFactory;
    }

    /**
     * Build an {@see AddressInterface}
     *
     * @return AddressInterface
     */
    public function build()
    {
        $country = $this->getCountryInformation($this->countryCode);
        $companyState = $this->getRegionCodeByCountryAndId($country, $this->regionId);
        $countryName = $country->getThreeLetterAbbreviation();

        /** @var AddressInterface $address */
        $address = $this->addressFactory->create();
        if (!empty($this->street)) {
            $address->setStreetAddress($this->street);
        }
        if (!empty($this->city)) {
            $address->setCity($this->city);
        }
        if ($companyState !== null) {
            $address->setMainDivision($companyState);
        }
        if (!empty($this->postalCode)) {
            $address->setPostalCode($this->zipCodeFixer->fix($this->postalCode));
        }
        if (!empty($countryName)) {
            $address->setCountry($countryName);
        }

        return $address;
    }

    /**
     * Set the City
     *
     * @param string $city
     * @return AddressBuilder
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Set the two-letter Country Code
     *
     * @param string $countryCode
     * @return AddressBuilder
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * Set the Postal Code
     *
     * @param string $postalCode
     * @return AddressBuilder
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * Set the Region ID
     *
     * @param string $regionId
     * @return AddressBuilder
     */
    public function setRegionId($regionId)
    {
        $this->regionId = $regionId;
        return $this;
    }

    /**
     * Set the Street Address
     *
     * @param string|string[] $rawStreet
     * @return AddressBuilder
     */
    public function setStreet($rawStreet)
    {
        if (!is_array($rawStreet)) {
            $rawStreet = [$rawStreet];
        }

        $street = [];
        foreach ($rawStreet as $rawLine) {
            if (!empty($rawLine)) {
                $street[] = $rawLine;
            }
        }

        $this->street = $street;

        return $this;
    }

    /**
     * Retrieve a country's information given its ID
     *
     * @param string $countryId Two letter country code
     * @return CountryInformationInterface
     */
    private function getCountryInformation($countryId)
    {
        try {
            return $this->countryInformationAcquirer->getCountryInfo($countryId);
        } catch (NoSuchEntityException $error) {
            return $this->countryInformationFactory->create();
        }
    }

    /**
     * Retrieve a region's code given its ID
     *
     * @param CountryInformationInterface $country
     * @param int $regionId
     *
     * @return string|null
     */
    private function getRegionCodeByCountryAndId(CountryInformationInterface $country, $regionId)
    {
        $regions = $country->getAvailableRegions();

        if ($regions === null) {
            return null;
        }

        // Linear search used since there exists no RegionInformationAcquirerInterface
        foreach ($regions as $region) {
            if ($region->getId() == $regionId) {
                return $region->getCode();
            }
        }

        return null;
    }
}
