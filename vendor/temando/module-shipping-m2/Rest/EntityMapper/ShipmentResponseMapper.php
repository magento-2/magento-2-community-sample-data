<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\DocumentationInterface;
use Temando\Shipping\Model\Shipment\CapabilityInterface;
use Temando\Shipping\Model\Shipment\CapabilityInterfaceFactory;
use Temando\Shipping\Model\Shipment\ExportDeclarationInterface;
use Temando\Shipping\Model\Shipment\ExportDeclarationInterfaceFactory;
use Temando\Shipping\Model\Shipment\FulfillmentInterface;
use Temando\Shipping\Model\Shipment\FulfillmentInterfaceFactory;
use Temando\Shipping\Model\Shipment\LocationInterface;
use Temando\Shipping\Model\Shipment\LocationInterfaceFactory;
use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\Shipment\ShipmentItemInterface;
use Temando\Shipping\Model\Shipment\ShipmentItemInterfaceFactory;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Model\ShipmentInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\Generic\Documentation;
use Temando\Shipping\Rest\Response\Type\Generic\Item;
use Temando\Shipping\Rest\Response\Type\Generic\Location;
use Temando\Shipping\Rest\Response\Type\Generic\Package;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill;
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
     * @var LocationInterfaceFactory
     */
    private $locationFactory;

    /**
     * @var FulfillmentInterfaceFactory
     */
    private $fulfillmentFactory;

    /**
     * @var ShipmentItemInterfaceFactory
     */
    private $shipmentItemFactory;

    /**
     * @var ExportDeclarationInterfaceFactory
     */
    private $exportDeclarationFactory;

    /**
     * @var PackageResponseMapper
     */
    private $packageMapper;

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
     * @param LocationInterfaceFactory $locationFactory
     * @param FulfillmentInterfaceFactory $fulfillmentFactory
     * @param ShipmentItemInterfaceFactory $shipmentItemFactory
     * @param ExportDeclarationInterfaceFactory $exportDeclarationFactory,
     * @param PackageResponseMapper $packageMapper
     * @param DocumentationResponseMapper $documentationMapper
     * @param CapabilityInterfaceFactory $capabilityFactory
     */
    public function __construct(
        ShipmentInterfaceFactory $shipmentFactory,
        LocationInterfaceFactory $locationFactory,
        FulfillmentInterfaceFactory $fulfillmentFactory,
        ShipmentItemInterfaceFactory $shipmentItemFactory,
        ExportDeclarationInterfaceFactory $exportDeclarationFactory,
        PackageResponseMapper $packageMapper,
        DocumentationResponseMapper $documentationMapper,
        CapabilityInterfaceFactory $capabilityFactory
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->locationFactory = $locationFactory;
        $this->fulfillmentFactory = $fulfillmentFactory;
        $this->shipmentItemFactory = $shipmentItemFactory;
        $this->exportDeclarationFactory = $exportDeclarationFactory;
        $this->packageMapper = $packageMapper;
        $this->documentationMapper = $documentationMapper;
        $this->capabilityFactory = $capabilityFactory;
    }

    /**
     * @param Location $apiLocation
     * @return LocationInterface
     */
    private function mapLocation(Location $apiLocation)
    {
        $contact = $apiLocation->getContact();
        $location = $this->locationFactory->create(['data' => [
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
            LocationInterface::TYPE => $apiLocation->getAddress()->getType(),
        ]]);

        return $location;
    }

    /**
     * @param Item[] $apiItems
     * @return ShipmentItemInterface[]
     */
    private function mapItems(array $apiItems)
    {
        $shipmentItems = array_map(function (Item $apiItem) {
            return $this->shipmentItemFactory->create(['data' => [
                ShipmentItemInterface::QTY => $apiItem->getQuantity(),
                ShipmentItemInterface::SKU => $apiItem->getProduct()->getSku(),
            ]]);
        }, $apiItems);

        return $shipmentItems;
    }

    /**
     * @param Package[] $apiPackages
     * @return PackageInterface[]
     */
    private function mapPackages(array $apiPackages)
    {
        // map collected packages
        $packages = array_map(function (Package $apiPackage) {
            return $this->packageMapper->map($apiPackage);
        }, $apiPackages);

        return $packages;
    }

    /**
     * @param ShipmentResponseType $apiShipment
     * @return DocumentationInterface[]
     */
    private function mapDocumentation(ShipmentResponseType $apiShipment)
    {
        // collect documentation from shipment and packages
        $apiDocs = $apiShipment->getAttributes()->getDocumentation();
        foreach ($apiShipment->getAttributes()->getPackages() as $package) {
            foreach ($package->getDocumentation() as $apiDoc) {
                $apiDocs[]= $apiDoc;
            }
        }

        // map collected documentation
        $documentation = array_map(function (Documentation $apiDoc) {
            return $this->documentationMapper->map($apiDoc);
        }, $apiDocs);

        return $documentation;
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
            $apiShipment->getAttributes()->getIsDutiable()
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
        $shipmentOrder       = $apiShipment->getAttributes()->getOrder();
        $isPaperless         = $apiShipment->getAttributes()->getIsPaperless();
        $status              = $apiShipment->getAttributes()->getStatus();
        $createdAt           = $apiShipment->getAttributes()->getCreatedAt();

        $documentation       = $this->mapDocumentation($apiShipment);
        $exportDeclaration   = $this->mapExportDeclaration($apiShipment);

        $shipmentFulfillment = $this->mapFulfillment($apiShipment->getAttributes()->getFulfill());
        $origin              = $this->mapLocation($apiShipment->getAttributes()->getOrigin());
        $destination         = $this->mapLocation($apiShipment->getAttributes()->getDestination());
        $items               = $this->mapItems($apiShipment->getAttributes()->getItems());
        $packages            = $this->mapPackages($apiShipment->getAttributes()->getPackages());
        $capabilities        = $this->mapCapabilities($apiShipment->getAttributes()->getCapabilities());

        if (!$apiShipment->getAttributes()->getFinalRecipient()) {
            $finalRecipient = null;
        } else {
            $finalRecipient = $this->mapLocation($apiShipment->getAttributes()->getFinalRecipient());
        }

        $shipment = $this->shipmentFactory->create(['data' => [
            ShipmentInterface::SHIPMENT_ID => $shipmentId,
            ShipmentInterface::ORDER_ID => $shipmentOrderId,
            ShipmentInterface::ORIGIN_ID => $shipmentOriginId,
            ShipmentInterface::CUSTOMER_REFERENCE => $shipmentOrder ? $shipmentOrder->getCustomerReference() : '',
            ShipmentInterface::ORIGIN_LOCATION => $origin,
            ShipmentInterface::DESTINATION_LOCATION => $destination,
            ShipmentInterface::FINAL_RECIPIENT_LOCATION => $finalRecipient,
            ShipmentInterface::FULFILLMENT => $shipmentFulfillment,
            ShipmentInterface::ITEMS => $items,
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
