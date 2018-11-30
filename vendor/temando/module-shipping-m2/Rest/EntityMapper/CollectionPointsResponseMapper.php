<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Api\Data\Delivery\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\Delivery\QuoteCollectionPointInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\CollectionPointQualification;
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
class CollectionPointsResponseMapper
{
    /**
     * @var ShippingExperiencesMapper
     */
    private $shippingExperiencesMapper;

    /**
     * @var QuoteCollectionPointInterfaceFactory
     */
    private $collectionPointFactory;

    /**
     * @var OpeningHoursMapper
     */
    private $openingHoursMapper;

    /**
     * CollectionPointsResponseMapper constructor.
     * @param ShippingExperiencesMapper $shippingExperiencesMapper
     * @param QuoteCollectionPointInterfaceFactory $collectionPointFactory
     * @param OpeningHoursMapper $openingHoursMapper
     */
    public function __construct(
        ShippingExperiencesMapper $shippingExperiencesMapper,
        QuoteCollectionPointInterfaceFactory $collectionPointFactory,
        OpeningHoursMapper $openingHoursMapper
    ) {
        $this->shippingExperiencesMapper = $shippingExperiencesMapper;
        $this->collectionPointFactory = $collectionPointFactory;
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
     * @param CollectionPointQualification[] $apiIncluded
     * @return QuoteCollectionPointInterface[]
     * @throws LocalizedException
     */
    public function map(array $apiIncluded)
    {
        /** @var CollectionPointQualification[] $apiIncluded */
        $apiIncluded = array_filter($apiIncluded, function (CollectionPointQualification $element) {
            return ($element->getType() == 'collection-point-quote-set');
        });

        /** @var \Temando\Shipping\Rest\Response\Fields\CollectionPointQualificationAttributes[] $sets */
        $sets = array_reduce($apiIncluded, function ($sets, CollectionPointQualification $apiIncluded) {
            return array_merge($sets, $apiIncluded->getAttributes());
        }, []);

        $collectionPoints = [];
        foreach ($sets as $set) {
            $location = $set->getCollectionPoint()->getLocation();
            $shippingExperiences = $this->shippingExperiencesMapper->map($set->getExperiences());
            $openingHours = $this->mapOpeningHours($location);

            $collectionPoint = $this->collectionPointFactory->create(['data' => [
                QuoteCollectionPointInterface::COLLECTION_POINT_ID => $set->getCollectionPoint()->getId(),
                QuoteCollectionPointInterface::NAME => $set->getCollectionPoint()->getName(),
                QuoteCollectionPointInterface::COUNTRY => $location->getAddress()->getCountryCode(),
                QuoteCollectionPointInterface::REGION => $location->getAddress()->getAdministrativeArea(),
                QuoteCollectionPointInterface::POSTCODE => $location->getAddress()->getPostalCode(),
                QuoteCollectionPointInterface::CITY => $location->getAddress()->getLocality(),
                QuoteCollectionPointInterface::STREET => $location->getAddress()->getLines(),
                QuoteCollectionPointInterface::OPENING_HOURS => $openingHours['general'],
                QuoteCollectionPointInterface::SHIPPING_EXPERIENCES => $shippingExperiences,
            ]]);

            $collectionPoints[]= $collectionPoint;
        }

        return $collectionPoints;
    }
}
