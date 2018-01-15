<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\Shipment\FulfillmentInterface;
use Temando\Shipping\Model\Shipment\FulfillmentInterfaceFactory;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Model\ShipmentInterfaceFactory;
use Temando\Shipping\Model\Shipment\ShipmentOriginInterface;
use Temando\Shipping\Model\Shipment\ShipmentOriginInterfaceFactory;
use Temando\Shipping\Model\Shipment\ShipmentDestinationInterface;
use Temando\Shipping\Model\Shipment\ShipmentDestinationInterfaceFactory;
use Temando\Shipping\Model\DocumentationCollection;
use Temando\Shipping\Model\DocumentationCollectionFactory;
use Temando\Shipping\Model\Shipment\PackageCollection;
use Temando\Shipping\Model\Shipment\PackageCollectionFactory;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Destination;
use Temando\Shipping\Rest\Response\Type\ShipmentResponseType;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentResponseMapper
{
    /**
     * @var ShipmentInterfaceFactory
     */
    private $shipmentFactory;

    /**
     * @var ShipmentOriginInterfaceFactory
     */
    private $originFactory;

    /**
     * @var ShipmentDestinationInterfaceFactory
     */
    private $destinationFactory;

    /**
     * @var FulfillmentInterfaceFactory
     */
    private $fulfillmentFactory;

    /**
     * @var PackageCollectionFactory
     */
    private $packageCollectionFactory;

    /**
     * @var PackageResponseMapper
     */
    private $packageMapper;

    /**
     * @var DocumentationCollectionFactory
     */
    private $documentationCollectionFactory;

    /**
     * @var DocumentationResponseMapper
     */
    private $documentationMapper;

    /**
     * ShipmentResponseMapper constructor.
     * @param ShipmentInterfaceFactory $shipmentFactory
     * @param ShipmentOriginInterfaceFactory $originFactory
     * @param ShipmentDestinationInterfaceFactory $destinationFactory
     * @param FulfillmentInterfaceFactory $fulfillmentFactory
     * @param PackageCollectionFactory $packageCollectionFactory
     * @param PackageResponseMapper $packageMapper
     * @param DocumentationCollectionFactory $documentationCollectionFactory
     * @param DocumentationResponseMapper $documentationMapper
     */
    public function __construct(
        ShipmentInterfaceFactory $shipmentFactory,
        ShipmentOriginInterfaceFactory $originFactory,
        ShipmentDestinationInterfaceFactory $destinationFactory,
        FulfillmentInterfaceFactory $fulfillmentFactory,
        PackageCollectionFactory $packageCollectionFactory,
        PackageResponseMapper $packageMapper,
        DocumentationCollectionFactory $documentationCollectionFactory,
        DocumentationResponseMapper $documentationMapper
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->originFactory = $originFactory;
        $this->destinationFactory = $destinationFactory;
        $this->fulfillmentFactory = $fulfillmentFactory;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->packageMapper = $packageMapper;
        $this->documentationCollectionFactory = $documentationCollectionFactory;
        $this->documentationMapper = $documentationMapper;
    }

    /**
     * @param Origin $origin
     * @return ShipmentOriginInterface
     */
    private function mapOriginLocation(Origin $origin)
    {
        $originLocation = $this->originFactory->create(['data' => [
            ShipmentOriginInterface::COMPANY => $origin->getContact()->getOrganisationName(),
            ShipmentOriginInterface::PERSON_FIRST_NAME => $origin->getContact()->getPersonFirstName(),
            ShipmentOriginInterface::PERSON_LAST_NAME => $origin->getContact()->getPersonLastName(),
            ShipmentOriginInterface::EMAIL => $origin->getContact()->getEmail(),
            ShipmentOriginInterface::PHONE_NUMBER => $origin->getContact()->getPhoneNumber(),
            ShipmentOriginInterface::STREET => $origin->getAddress()->getLines(),
            ShipmentOriginInterface::CITY => $origin->getAddress()->getLocality(),
            ShipmentOriginInterface::POSTAL_CODE => $origin->getAddress()->getPostalCode(),
            ShipmentOriginInterface::REGION_CODE => $origin->getAddress()->getAdministrativeArea(),
            ShipmentOriginInterface::COUNTRY_CODE => $origin->getAddress()->getCountryCode(),
        ]]);

        return $originLocation;
    }

    /**
     * @param Destination $destination
     * @return ShipmentDestinationInterface
     */
    private function mapDestinationLocation(Destination $destination)
    {
        $destinationLocation = $this->destinationFactory->create(['data' => [
            ShipmentDestinationInterface::COMPANY => $destination->getContact()->getOrganisationName(),
            ShipmentDestinationInterface::PERSON_FIRST_NAME => $destination->getContact()->getPersonFirstName(),
            ShipmentDestinationInterface::PERSON_LAST_NAME => $destination->getContact()->getPersonLastName(),
            ShipmentDestinationInterface::EMAIL => $destination->getContact()->getEmail(),
            ShipmentDestinationInterface::PHONE_NUMBER => $destination->getContact()->getPhoneNumber(),
            ShipmentDestinationInterface::STREET => $destination->getAddress()->getLines(),
            ShipmentDestinationInterface::CITY => $destination->getAddress()->getLocality(),
            ShipmentDestinationInterface::POSTAL_CODE => $destination->getAddress()->getPostalCode(),
            ShipmentDestinationInterface::REGION_CODE => $destination->getAddress()->getAdministrativeArea(),
            ShipmentDestinationInterface::COUNTRY_CODE => $destination->getAddress()->getCountryCode(),
        ]]);

        return $destinationLocation;
    }

    /**
     * @param ShipmentResponseType $apiShipment
     * @return PackageCollection
     */
    private function mapPackages(ShipmentResponseType $apiShipment)
    {
        $packageCollection = $this->packageCollectionFactory->create();

        // collect packages from shipment
        $apiPackages = $apiShipment->getAttributes()->getPackages();

        // map collected packages
        foreach ($apiPackages as $apiPackage) {
            $package = $this->packageMapper->map($apiPackage);
            $packageCollection->offsetSet($package->getPackageId(), $package);
        }

        return $packageCollection;
    }

    /**
     * @param ShipmentResponseType $apiShipment
     * @return DocumentationCollection
     */
    private function mapDocumentation(ShipmentResponseType $apiShipment)
    {
        $documentationCollection = $this->documentationCollectionFactory->create();

        // collect documentation from shipment and packages
        $apiDocs = $apiShipment->getAttributes()->getDocumentation();
        foreach ($apiShipment->getAttributes()->getPackages() as $package) {
            foreach ($package->getDocumentation() as $apiDoc) {
                $apiDocs[]= $apiDoc;
            }
        }

        // map collected documentation
        foreach ($apiDocs as $apiDoc) {
            $documentation = $this->documentationMapper->map($apiDoc);
            $documentationCollection->offsetSet($documentation->getDocumentationId(), $documentation);
        }

        return $documentationCollection;
    }

    /**
     * @param Fulfill | null $apiFulfillment
     *
     * @return FulfillmentInterface
     */
    private function mapFulfillment($apiFulfillment)
    {
        /** @var \Temando\Shipping\Model\Shipment\Fulfillment $fulfillment */
        $fulfillment = $this->fulfillmentFactory->create();

        if ($apiFulfillment) {
            $fulfillment->setData(
                FulfillmentInterface::TRACKING_REFERENCE,
                $apiFulfillment->getCarrierBooking()->getTrackingReference()
            );

            $fulfillment->setData(
                FulfillmentInterface::SERVICE_NAME,
                $apiFulfillment->getCarrierBooking()->getServiceName()
            );
        }

        return $fulfillment;
    }

    /**
     * @param ShipmentResponseType $apiShipment
     * @return ShipmentInterface
     */
    public function map(ShipmentResponseType $apiShipment)
    {
        $shipmentId          = $apiShipment->getId();
        $shipmentOrderId     = $apiShipment->getAttributes()->getOrderId();
        $shipmentOriginId    = $apiShipment->getAttributes()->getOriginId();
        $shipmentFulfillment = $this->mapFulfillment($apiShipment->getAttributes()->getFulfill());
        $originLocation      = $this->mapOriginLocation($apiShipment->getAttributes()->getOrigin());
        $destinationLocation = $this->mapDestinationLocation($apiShipment->getAttributes()->getDestination());
        $packages            = $this->mapPackages($apiShipment);
        $documentation       = $this->mapDocumentation($apiShipment);
        $isPaperless         = $apiShipment->getAttributes()->getIsPaperless();

        $shipment = $this->shipmentFactory->create(['data' => [
            ShipmentInterface::SHIPMENT_ID => $shipmentId,
            ShipmentInterface::ORDER_ID => $shipmentOrderId,
            ShipmentInterface::ORIGIN_ID => $shipmentOriginId,
            ShipmentInterface::ORIGIN_LOCATION => $originLocation,
            ShipmentInterface::DESTINATION_LOCATION => $destinationLocation,
            ShipmentInterface::FULFILLMENT => $shipmentFulfillment,
            ShipmentInterface::PACKAGES => $packages,
            ShipmentInterface::DOCUMENTATION => $documentation,
            ShipmentInterface::IS_PAPERLESS => $isPaperless,
        ]]);

        return $shipment;
    }
}
