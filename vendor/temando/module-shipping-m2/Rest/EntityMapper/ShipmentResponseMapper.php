<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\DocumentationCollection;
use Temando\Shipping\Model\DocumentationCollectionFactory;
use Temando\Shipping\Model\Shipment\CapabilityInterface;
use Temando\Shipping\Model\Shipment\CapabilityInterfaceFactory;
use Temando\Shipping\Model\Shipment\ExportDeclarationInterface;
use Temando\Shipping\Model\Shipment\ExportDeclarationInterfaceFactory;
use Temando\Shipping\Model\Shipment\FulfillmentInterface;
use Temando\Shipping\Model\Shipment\FulfillmentInterfaceFactory;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Model\ShipmentInterfaceFactory;
use Temando\Shipping\Model\Shipment\PackageCollection;
use Temando\Shipping\Model\Shipment\PackageCollectionFactory;
use Temando\Shipping\Model\Shipment\ShipmentOriginInterface;
use Temando\Shipping\Model\Shipment\ShipmentOriginInterfaceFactory;
use Temando\Shipping\Model\Shipment\ShipmentDestinationInterface;
use Temando\Shipping\Model\Shipment\ShipmentDestinationInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Destination;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package;
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
     * @var ExportDeclarationInterfaceFactory
     */
    private $exportDeclarationFactory;

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
     * @var CapabilityInterfaceFactory
     */
    private $capabilityFactory;

    /**
     * ShipmentResponseMapper constructor.
     * @param ShipmentInterfaceFactory $shipmentFactory
     * @param ShipmentOriginInterfaceFactory $originFactory
     * @param ShipmentDestinationInterfaceFactory $destinationFactory
     * @param FulfillmentInterfaceFactory $fulfillmentFactory
     * @param PackageCollectionFactory $packageCollectionFactory
     * @param ExportDeclarationInterfaceFactory $exportDeclarationFactory,
     * @param PackageResponseMapper $packageMapper
     * @param DocumentationCollectionFactory $documentationCollectionFactory
     * @param DocumentationResponseMapper $documentationMapper
     * @param CapabilityInterfaceFactory $capabilityFactory
     */
    public function __construct(
        ShipmentInterfaceFactory $shipmentFactory,
        ShipmentOriginInterfaceFactory $originFactory,
        ShipmentDestinationInterfaceFactory $destinationFactory,
        FulfillmentInterfaceFactory $fulfillmentFactory,
        PackageCollectionFactory $packageCollectionFactory,
        ExportDeclarationInterfaceFactory $exportDeclarationFactory,
        PackageResponseMapper $packageMapper,
        DocumentationCollectionFactory $documentationCollectionFactory,
        DocumentationResponseMapper $documentationMapper,
        CapabilityInterfaceFactory $capabilityFactory
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->originFactory = $originFactory;
        $this->destinationFactory = $destinationFactory;
        $this->fulfillmentFactory = $fulfillmentFactory;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->exportDeclarationFactory = $exportDeclarationFactory;
        $this->packageMapper = $packageMapper;
        $this->documentationCollectionFactory = $documentationCollectionFactory;
        $this->documentationMapper = $documentationMapper;
        $this->capabilityFactory = $capabilityFactory;
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
            ShipmentDestinationInterface::TYPE => $destination->getAddress()->getType(),
        ]]);

        return $destinationLocation;
    }

    /**
     * @param Package[] $apiPackages
     * @return PackageCollection
     */
    private function mapPackages(array $apiPackages)
    {
        $packageCollection = $this->packageCollectionFactory->create();

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

            $fulfillment->setData(
                FulfillmentInterface::CARRIER_NAME,
                $apiFulfillment->getCarrierBooking()->getCarrierName()
            );
        }

        return $fulfillment;
    }

    /**
     * @param ShipmentResponseType | null $apiShipment
     *
     * @return ExportDeclarationInterface
     */
    private function mapExportDeclaration(ShipmentResponseType $apiShipment)
    {
        $apiDeclaration = $apiShipment->getAttributes()->getExportDeclaration();
        if (!$apiDeclaration) {
            return null;
        }

        /** @var \Temando\Shipping\Model\Shipment\ExportDeclaration $exportDeclaration */
        $exportDeclaration = $this->exportDeclarationFactory->create();

        $exportDeclaration->setData(
            ExportDeclarationInterface::IS_DUTIABLE,
            $apiShipment->getAttributes()->isDutiable()
        );

        $declaredValue = sprintf(
            '%s %s',
            $apiDeclaration->getDeclaredValue()->getAmount(),
            $apiDeclaration->getDeclaredValue()->getCurrency()
        );
        $exportDeclaration->setData(
            ExportDeclarationInterface::DECLARED_VALUE,
            $declaredValue
        );

        $exportDeclaration->setData(
            ExportDeclarationInterface::EXPORT_CATEGORY,
            $apiDeclaration->getExportCategory()
        );

        $exportDeclaration->setData(
            ExportDeclarationInterface::EXPORT_REASON,
            $apiDeclaration->getExportReason()
        );

        $exportDeclaration->setData(
            ExportDeclarationInterface::INCOTERM,
            $apiDeclaration->getIncoterm()
        );

        // dependent properties: signatory
        $apiSignatory = $apiDeclaration->getSignatory();
        if ($apiSignatory) {
            $exportDeclaration->setData(
                ExportDeclarationInterface::SIGNATORY_PERSON_TITLE,
                $apiSignatory->getPersonTitle()
            );

            $exportDeclaration->setData(
                ExportDeclarationInterface::SIGNATORY_PERSON_FIRST_NAME,
                $apiSignatory->getPersonFirstName()
            );

            $exportDeclaration->setData(
                ExportDeclarationInterface::SIGNATORY_PERSON_LAST_NAME,
                $apiSignatory->getPersonLastName()
            );
        }

        // dependent properties: export codes
        $apiExportCodes = $apiDeclaration->getExportCodes();
        if ($apiExportCodes) {
            $exportDeclaration->setData(
                ExportDeclarationInterface::EDN,
                $apiExportCodes->getExportDeclarationNumber()
            );

            $exportDeclaration->setData(
                ExportDeclarationInterface::EEI,
                $apiExportCodes->getElectronicExportInformation()
            );

            $exportDeclaration->setData(
                ExportDeclarationInterface::ITN,
                $apiExportCodes->getInternalTransactionNumber()
            );

            $exportDeclaration->setData(
                ExportDeclarationInterface::EEL,
                $apiExportCodes->getExemptionExclusionLegend()
            );
        }

        return $exportDeclaration;
    }

    /**
     * @param mixed[][] $apiCapabilities
     *
     * @return CapabilityInterface[]
     */
    public function mapCapabilities(array $apiCapabilities)
    {
        $capabilities = [];

        foreach ($apiCapabilities as $capabilityCode => $capabilityProperties) {
            if (!is_array($capabilityProperties)) {
                $capabilityProperties = [$capabilityProperties];
            }

            $capability = $this->capabilityFactory->create(['data' => [
                CapabilityInterface::CAPABILITY_ID => $capabilityCode,
                CapabilityInterface::PROPERTIES => $capabilityProperties
            ]]);

            $capabilities[]= $capability;
        }

        return $capabilities;
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
        $isPaperless         = $apiShipment->getAttributes()->getIsPaperless();
        $status              = $apiShipment->getAttributes()->getStatus();
        $createdAt           = $apiShipment->getAttributes()->getCreatedAt();

        $documentation       = $this->mapDocumentation($apiShipment);
        $exportDeclaration   = $this->mapExportDeclaration($apiShipment);

        $shipmentFulfillment = $this->mapFulfillment($apiShipment->getAttributes()->getFulfill());
        $originLocation      = $this->mapOriginLocation($apiShipment->getAttributes()->getOrigin());
        $destinationLocation = $this->mapDestinationLocation($apiShipment->getAttributes()->getDestination());
        $packages            = $this->mapPackages($apiShipment->getAttributes()->getPackages());
        $capabilities        = $this->mapCapabilities($apiShipment->getAttributes()->getCapabilities());

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
            ShipmentInterface::EXPORT_DECLARATION => $exportDeclaration,
            ShipmentInterface::STATUS => $status,
            ShipmentInterface::CAPABILITIES => $capabilities,
            ShipmentInterface::CREATED_AT => $createdAt,
        ]]);

        return $shipment;
    }
}
