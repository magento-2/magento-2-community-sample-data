<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\Location;
use Temando\Shipping\Rest\Response\DataObject\OrderQualification;
use Temando\Shipping\Rest\Response\Fields\Location\OpeningHours;
use Temando\Shipping\Rest\Response\Fields\LocationAttributes;

/**
 * Map API data to application data object
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class PickupLocationsResponseMapper
{
    /**
     * @var ShippingExperiencesMapper
     */
    private $shippingExperiencesMapper;

    /**
     * @var QuotePickupLocationInterfaceFactory
     */
    private $pickupLocationFactory;

    /**
     * @var OpeningHoursMapper
     */
    private $openingHoursMapper;

    /**
     * PickupLocationsResponseMapper constructor.
     * @param ShippingExperiencesMapper $shippingExperiencesMapper
     * @param QuotePickupLocationInterfaceFactory $pickupLocationFactory
     * @param OpeningHoursMapper $openingHoursMapper
     */
    public function __construct(
        ShippingExperiencesMapper $shippingExperiencesMapper,
        QuotePickupLocationInterfaceFactory $pickupLocationFactory,
        OpeningHoursMapper $openingHoursMapper
    ) {
        $this->shippingExperiencesMapper = $shippingExperiencesMapper;
        $this->pickupLocationFactory = $pickupLocationFactory;
        $this->openingHoursMapper = $openingHoursMapper;
    }

    /**
     * @param LocationAttributes $apiLocation
     * @return string[][]
     */
    private function mapOpeningHours(LocationAttributes $apiLocation)
    {
        if ($apiLocation->getOpeningHours() instanceof OpeningHours) {
            return $this->openingHoursMapper->map($apiLocation->getOpeningHours());
        } else {
            return [
                'general' => [],
                'specific' => [],
            ];
        }
    }

    /**
     * @param OrderQualification[]|Location[] $apiIncluded
     * @return QuotePickupLocationInterface[]
     * @throws LocalizedException
     */
    public function map(array $apiIncluded)
    {
        /** @var OrderQualification[] $apiQualifications */
        $apiQualifications = array_filter($apiIncluded, function ($includedResource) {
            return ($includedResource instanceof OrderQualification);
        });

        $pickupLocations = [];
        foreach ($apiQualifications as $apiQualification) {
            /** @var Location $location */
            $location = current($apiQualification->getLocations());
            $locationAddress = $location->getAttributes()->getAddress();
            $openingHours = $this->mapOpeningHours($location->getAttributes());
            $shippingExperiences = $this->shippingExperiencesMapper->map(
                $apiQualification->getAttributes()->getExperiences()
            );

            $pickupLocation = $this->pickupLocationFactory->create(['data' => [
                QuotePickupLocationInterface::PICKUP_LOCATION_ID => $location->getId(),
                QuotePickupLocationInterface::NAME => $location->getAttributes()->getName(),
                QuotePickupLocationInterface::COUNTRY => $locationAddress->getCountryCode(),
                QuotePickupLocationInterface::REGION => $locationAddress->getAdministrativeArea(),
                QuotePickupLocationInterface::POSTCODE => $locationAddress->getPostalCode(),
                QuotePickupLocationInterface::CITY => $locationAddress->getLocality(),
                QuotePickupLocationInterface::STREET => $locationAddress->getLines(),
                QuotePickupLocationInterface::OPENING_HOURS => $openingHours,
                QuotePickupLocationInterface::SHIPPING_EXPERIENCES => $shippingExperiences,
            ]]);

            $pickupLocations[]= $pickupLocation;
        }

        return $pickupLocations;
    }
}
