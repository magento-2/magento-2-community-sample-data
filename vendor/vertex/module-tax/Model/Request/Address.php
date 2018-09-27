<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Directory\Api\Data\CountryInformationInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Vertex\Tax\Model\ZipCodeFixer;

/**
 * Address data formatter for Vertex API Calls
 */
class Address
{
    /** @var CountryInformationAcquirerInterface */
    private $countryInformationAcquirer;

    /** @var CountryInformationInterfaceFactory */
    private $countryInformationFactory;

    /** @var ZipCodeFixer */
    private $zipCodeFixer;

    /**
     * @param CountryInformationAcquirerInterface $countryInformationAcquirer
     * @param CountryInformationInterfaceFactory $countryInformationFactory
     * @param ZipCodeFixer $zipCodeFixer
     */
    public function __construct(
        CountryInformationAcquirerInterface $countryInformationAcquirer,
        CountryInformationInterfaceFactory $countryInformationFactory,
        ZipCodeFixer $zipCodeFixer
    ) {
        $this->countryInformationAcquirer = $countryInformationAcquirer;
        $this->countryInformationFactory = $countryInformationFactory;
        $this->zipCodeFixer = $zipCodeFixer;
    }

    /**
     * Create a properly formatted array of Address Data for the Vertex API
     *
     * @param string|string[] $street
     * @param string $city
     * @param string $regionId
     * @param string $postalCode
     * @param string $countryCode Two letter country code
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedAddressData($street, $city, $regionId, $postalCode, $countryCode)
    {
        $country = $this->getCountryInformation($countryCode);
        $companyState = $this->getRegionCodeByCountryAndId($country, $regionId);
        $countryName = $country->getThreeLetterAbbreviation();
        $street2 = '';

        if (is_array($street)) {
            $street1 = $street[0];
            if (isset($street[1])) {
                $street2 = $street[1];
            }
        } else {
            $street1 = $street;
        }

        $data = [
            'StreetAddress1' => $street1 ?: '',
            'StreetAddress2' => $street2 ?: '',
            'City' => $city ?: '',
            'MainDivision' => $companyState ?: '',
            'PostalCode' => $this->zipCodeFixer->fix($postalCode) ?: '',
            'Country' => $countryName ?: ''
        ];

        return $data;
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
}
