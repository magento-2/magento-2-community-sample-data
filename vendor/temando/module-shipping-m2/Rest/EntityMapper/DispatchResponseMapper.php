<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\Dispatch\ErrorInterfaceFactory;
use Temando\Shipping\Model\Dispatch\PickupChargeInterface;
use Temando\Shipping\Model\Dispatch\PickupChargeInterfaceFactory;
use Temando\Shipping\Model\Dispatch\ShipmentInterface;
use Temando\Shipping\Model\Dispatch\ShipmentInterfaceFactory;
use Temando\Shipping\Model\DispatchInterface;
use Temando\Shipping\Model\DispatchInterfaceFactory;
use Temando\Shipping\Model\Shipment\ShipmentErrorInterface;
use Temando\Shipping\Rest\Response\DataObject\Completion;
use Temando\Shipping\Rest\Response\Fields\Completion\Group\Charge;
use Temando\Shipping\Rest\Response\Fields\Completion\Shipment;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchResponseMapper
{
    /**
     * @var DispatchInterfaceFactory
     */
    private $dispatchFactory;

    /**
     * @var ErrorInterfaceFactory
     */
    private $dispatchErrorFactory;

    /**
     * @var ShipmentInterfaceFactory
     */
    private $shipmentFactory;

    /**
     * @var PickupChargeInterfaceFactory
     */
    private $pickupChargeFactory;

    /**
     * @var DocumentationResponseMapper
     */
    private $documentationMapper;

    /**
     * DispatchResponseMapper constructor.
     * @param DispatchInterfaceFactory $dispatchFactory
     * @param ErrorInterfaceFactory $dispatchErrorFactory
     * @param ShipmentInterfaceFactory $shipmentFactory
     * @param PickupChargeInterfaceFactory $pickupChargeFactory
     * @param DocumentationResponseMapper $documentationMapper
     */
    public function __construct(
        DispatchInterfaceFactory $dispatchFactory,
        ErrorInterfaceFactory $dispatchErrorFactory,
        ShipmentInterfaceFactory $shipmentFactory,
        PickupChargeInterfaceFactory $pickupChargeFactory,
        DocumentationResponseMapper $documentationMapper
    ) {
        $this->dispatchFactory = $dispatchFactory;
        $this->dispatchErrorFactory = $dispatchErrorFactory;
        $this->shipmentFactory = $shipmentFactory;
        $this->pickupChargeFactory = $pickupChargeFactory;
        $this->documentationMapper = $documentationMapper;
    }

    /**
     * @param Shipment $apiShipment
     * @return ShipmentInterface
     */
    private function mapShipment(Shipment $apiShipment)
    {
        $errors = [];
        foreach ($apiShipment->getErrors() as $apiError) {
            $errors[]= $this->dispatchErrorFactory->create(['data' => [
                ShipmentErrorInterface::TITLE => $apiError->getTitle(),
                ShipmentErrorInterface::DETAIL => $apiError->getDetail(),
            ]]);
        }

        $shipment = $this->shipmentFactory->create(['data' => [
            ShipmentInterface::SHIPMENT_ID => $apiShipment->getId(),
            ShipmentInterface::STATUS => $apiShipment->getStatus(),
            ShipmentInterface::MESSAGE => $apiShipment->getMessage(),
            ShipmentInterface::ERRORS => $errors,
        ]]);

        return $shipment;
    }

    /**
     * @param Charge $apiCharge
     * @return PickupChargeInterface
     */
    private function mapPickupCharge(Charge $apiCharge)
    {
        $pickupCharge = $this->pickupChargeFactory->create(['data' => [
            PickupChargeInterface::DESCRIPTION => (string) $apiCharge->getDescription(),
            PickupChargeInterface::AMOUNT => (float) $apiCharge->getAmount()->getAmount(),
            PickupChargeInterface::CURRENCY => (string) $apiCharge->getAmount()->getCurrency(),
        ]]);

        return $pickupCharge;
    }

    /**
     * @param Completion $apiCompletion
     * @return DispatchInterface
     */
    public function map(Completion $apiCompletion)
    {
        $dispatchId = $apiCompletion->getId();
        $status = $apiCompletion->getAttributes()->getStatus();
        $customAttributes = $apiCompletion->getAttributes()->getCustomAttributes();
        $carrierName = $customAttributes ? $customAttributes->getCarrierName() : 'No Carrier Found';
        $createdAtDate = $apiCompletion->getAttributes()->getCreatedAt();
        $readyAtDate = $apiCompletion->getAttributes()->getReadyAt();
        $carrierMessages = [];
        $pickupReferences = [];
        $pickupCharges = [];
        $failedShipments = [];
        $includedShipments = [];
        $documentation = [];

        // split shipments into failed and successfully dispatched
        foreach ($apiCompletion->getAttributes()->getShipments() as $apiShipment) {
            if ($apiShipment->getStatus() === 'error') {
                $failedShipments[$apiShipment->getId()] = $this->mapShipment($apiShipment);
            } else {
                $includedShipments[$apiShipment->getId()] = $this->mapShipment($apiShipment);
            }
        }

        // map collected documentation
        foreach ($apiCompletion->getAttributes()->getGroups() as $attributeGroup) {
            $carrierMessages[]= $attributeGroup->getCarrierMessage();
            $pickupReferences[]= $attributeGroup->getPickupReference();

            foreach ($attributeGroup->getCharges() as $pickupCharge) {
                $pickupCharges[]= $this->mapPickupCharge($pickupCharge);
            }

            foreach ($attributeGroup->getDocumentation() as $apiDoc) {
                $documentation[]= $this->documentationMapper->map($apiDoc);
            }
        }

        $carrierMessages = array_filter($carrierMessages);
        $pickupReferences = array_filter($pickupReferences);
        sort($pickupReferences);

        $dispatch = $this->dispatchFactory->create(['data' => [
            DispatchInterface::DISPATCH_ID => $dispatchId,
            DispatchInterface::STATUS => $status,
            DispatchInterface::CARRIER_NAME => $carrierName,
            DispatchInterface::CARRIER_MESSAGES => $carrierMessages,
            DispatchInterface::CREATED_AT_DATE => $createdAtDate,
            DispatchInterface::READY_AT_DATE => $readyAtDate,
            DispatchInterface::PICKUP_NUMBERS => $pickupReferences,
            DispatchInterface::PICKUP_CHARGES => $pickupCharges,
            DispatchInterface::INCLUDED_SHIPMENTS => $includedShipments,
            DispatchInterface::FAILED_SHIPMENTS => $failedShipments,
            DispatchInterface::DOCUMENTATION => $documentation,
        ]]);

        return $dispatch;
    }
}
