<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location\OpeningHours;
use Temando\Shipping\Rest\Response\Type\CollectionPointsIncludedResponseType;

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
     * CollectionPointsResponseMapper constructor.
     * @param ShippingExperiencesMapper $shippingExperiencesMapper
     * @param QuoteCollectionPointInterfaceFactory $collectionPointFactory
     */
    public function __construct(
        ShippingExperiencesMapper $shippingExperiencesMapper,
        QuoteCollectionPointInterfaceFactory $collectionPointFactory
    ) {
        $this->shippingExperiencesMapper = $shippingExperiencesMapper;
        $this->collectionPointFactory = $collectionPointFactory;
    }

    /**
     * @param OpeningHours $apiHours
     * @return string[]
     *
     * Date Format: ["l" => ["from" => "H:i:sP", "to" => "H:i:sP"]]
     */
    private function mapOpeningHours(OpeningHours $apiHours)
    {
        $openingHours = [];

        foreach ($apiHours->getDefault() as $item) {
            $dow = $item->getDayOfWeek();
            $openingHours[$dow] = [
                'from' => $item->getOpens(),
                'to' => $item->getCloses(),
            ];
        }

        return $openingHours;
    }

    /**
     * @param CollectionPointsIncludedResponseType[] $apiIncluded
     * @return QuoteCollectionPointInterface[]
     */
    public function map(array $apiIncluded)
    {
        /** @var CollectionPointsIncludedResponseType[] $apiIncluded */
        $apiIncluded = array_filter($apiIncluded, function (CollectionPointsIncludedResponseType $element) {
            return ($element->getType() == 'collection-point-quote-set');
        });

        /** @var \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes[] $sets */
        $sets = array_reduce($apiIncluded, function ($sets, CollectionPointsIncludedResponseType $apiIncluded) {
            return array_merge($sets, $apiIncluded->getAttributes());
        }, []);

        $collectionPoints = [];
        foreach ($sets as $set) {
            $location = $set->getCollectionPoint()->getLocation();
            $openingHours = $this->mapOpeningHours($location->getOpeningHours());
            $shippingExperiences = $this->shippingExperiencesMapper->map($set->getExperiences());

            $collectionPoint = $this->collectionPointFactory->create(['data' => [
                QuoteCollectionPointInterface::COLLECTION_POINT_ID => $set->getCollectionPoint()->getId(),
                QuoteCollectionPointInterface::NAME => $set->getCollectionPoint()->getName(),
                QuoteCollectionPointInterface::COUNTRY => $location->getAddress()->getCountryCode(),
                QuoteCollectionPointInterface::REGION => $location->getAddress()->getAdministrativeArea(),
                QuoteCollectionPointInterface::POSTCODE => $location->getAddress()->getPostalCode(),
                QuoteCollectionPointInterface::CITY => $location->getAddress()->getLocality(),
                QuoteCollectionPointInterface::STREET => $location->getAddress()->getLines(),
                QuoteCollectionPointInterface::OPENING_HOURS => $openingHours,
                QuoteCollectionPointInterface::SHIPPING_EXPERIENCES => $shippingExperiences,
            ]]);

            $collectionPoints[]= $collectionPoint;
        }

        return $collectionPoints;
    }
}
