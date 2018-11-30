<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\Model\PickupInterfaceFactory;
use Temando\Shipping\Model\Shipment\LocationInterface;
use Temando\Shipping\Model\Shipment\LocationInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\Fulfillment;
use Temando\Shipping\Rest\Response\Fields\Fulfillment\Item;
use Temando\Shipping\Rest\Response\Fields\Location\OpeningHours;
use Temando\Shipping\Rest\Response\Fields\LocationAttributes;
use Temando\Shipping\Rest\Response\Fields\Relationship;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class FulfillmentResponseMapper implements PointerAwareInterface
{
    /**
     * @var LocationInterfaceFactory
     */
    private $locationFactory;

    /**
     * @var PickupInterfaceFactory
     */
    private $pickupFactory;

    /**
     * @var OpeningHoursMapper
     */
    private $openingHoursMapper;

    /**
     * FulfillmentResponseMapper constructor.
     * @param LocationInterfaceFactory $locationFactory
     * @param PickupInterfaceFactory $pickupFactory
     * @param OpeningHoursMapper $openingHoursMapper
     */
    public function __construct(
        LocationInterfaceFactory $locationFactory,
        PickupInterfaceFactory $pickupFactory,
        OpeningHoursMapper $openingHoursMapper
    ) {
        $this->locationFactory = $locationFactory;
        $this->pickupFactory = $pickupFactory;
        $this->openingHoursMapper = $openingHoursMapper;
    }

    /**
     * @param Relationship[] $relationships
     * @return string
     */
    private function extractOrderId(array $relationships)
    {
        foreach ($relationships as $relationship) {
            foreach ($relationship->getData() as $resourceIdentifier) {
                if ($resourceIdentifier->getType() === 'order') {
                    return $resourceIdentifier->getId();
                }
            };
        }

        return '';
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
     * @param LocationAttributes $apiLocation
     * @return LocationInterface
     */
    private function mapLocation(LocationAttributes $apiLocation)
    {
        $contact = $apiLocation->getContact();
        $openingHours = $this->mapOpeningHours($apiLocation);

        $location = $this->locationFactory->create(['data' => [
            LocationInterface::NAME => (string)$apiLocation->getName(),
            LocationInterface::COMPANY => $contact ? $contact->getOrganisationName() : '',
            LocationInterface::PERSON_FIRST_NAME => $contact ? $contact->getPersonFirstName() : '',
            LocationInterface::PERSON_LAST_NAME => $contact ? $contact->getPersonLastName() : '',
            LocationInterface::EMAIL => $contact ? $contact->getEmail() : '',
            LocationInterface::PHONE_NUMBER => $contact ? $contact->getPhoneNumber() : '',
            LocationInterface::STREET => $apiLocation->getAddress()->getLines(),
            LocationInterface::CITY => $apiLocation->getAddress()->getLocality(),
            LocationInterface::POSTAL_CODE => $apiLocation->getAddress()->getPostalCode(),
            LocationInterface::REGION_CODE => $apiLocation->getAddress()->getAdministrativeArea(),
            LocationInterface::COUNTRY_CODE => $apiLocation->getAddress()->getCountryCode(),
            LocationInterface::TYPE => (string)$apiLocation->getType(),
            LocationInterface::OPENING_HOURS => $openingHours,
        ]]);

        return $location;
    }

    /**
     * @param Item[] $apiItems
     * @return string[]
     */
    private function mapPickupItems(array $apiItems)
    {
        $items = [];

        foreach ($apiItems as $apiItem) {
            $items[$apiItem->getProduct()->getSku()] = $apiItem->getQuantity();
        }

        return $items;
    }

    /**
     * @param Fulfillment $apiFulfillment
     * @return PickupInterface
     */
    public function mapPickup(Fulfillment $apiFulfillment)
    {
        $orderId = $this->extractOrderId($apiFulfillment->getRelationships());

        /** @var \Magento\Sales\Model\Order\Address $shippingAddress */
        $items = $this->mapPickupItems($apiFulfillment->getAttributes()->getItems());
        $pickupLocation = $this->mapLocation($apiFulfillment->getAttributes()->getPickUpLocation());

        $pickup = $this->pickupFactory->create(['data' => [
            PickupInterface::PICKUP_ID => $apiFulfillment->getId(),
            PickupInterface::STATE => $apiFulfillment->getAttributes()->getState(),
            PickupInterface::ORDER_ID => $orderId,
            PickupInterface::ORDER_REFERENCE => $apiFulfillment->getAttributes()->getReference(),
            PickupInterface::CREATED_AT => $apiFulfillment->getAttributes()->getCreatedAt(),
            PickupInterface::READY_AT => $apiFulfillment->getAttributes()->getReadyAt(),
            PickupInterface::PICKUP_LOCATION => $pickupLocation,
            PickupInterface::ITEMS => $items,
        ]]);

        return $pickup;
    }

    /**
     * Obtain the JSON pointer to a platform property that corresponds to a local entity property.
     *
     * @param string $property The local property name
     * @return string The platform property pointer
     * @throws LocalizedException
     */
    public function getPath($property)
    {
        $pathMap = [
            PickupInterface::CREATED_AT => '/createdAt',
            PickupInterface::READY_AT => '/readyAt',
            PickupInterface::PICKUP_LOCATION => '/pickUpLocation',
            PickupInterface::ORDER_ID => '/order',
            PickupInterface::STATE => '/state',
        ];

        if (!isset($pathMap[$property])) {
            throw new LocalizedException(__('Filter field %1 is not supported by the API.', $property));
        }

        return $pathMap[$property];
    }
}
