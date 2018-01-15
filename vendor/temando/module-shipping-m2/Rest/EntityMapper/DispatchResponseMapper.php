<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\Dispatch\ShipmentInterface;
use Temando\Shipping\Model\Dispatch\ShipmentInterfaceFactory;
use Temando\Shipping\Model\DispatchInterface;
use Temando\Shipping\Model\DispatchInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\Completion\Attributes\Shipment;
use Temando\Shipping\Rest\Response\Type\CompletionResponseType;
use Temando\Shipping\Model\DocumentationCollectionFactory;

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
     * @var DocumentationCollectionFactory
     */
    private $documentationCollectionFactory;

    /**
     * @var DocumentationResponseMapper
     */
    private $documentationMapper;

    /**
     * DispatchResponseMapper constructor.
     * @param DispatchInterfaceFactory $dispatchFactory
     * @param ShipmentInterfaceFactory $shipmentFactory
     * @param DocumentationCollectionFactory $documentationCollectionFactory
     * @param DocumentationResponseMapper $documentationMapper
     */
    public function __construct(
        DispatchInterfaceFactory $dispatchFactory,
        ShipmentInterfaceFactory $shipmentFactory,
        DocumentationCollectionFactory $documentationCollectionFactory,
        DocumentationResponseMapper $documentationMapper
    ) {
        $this->dispatchFactory = $dispatchFactory;
        $this->shipmentFactory = $shipmentFactory;
        $this->documentationCollectionFactory = $documentationCollectionFactory;
        $this->documentationMapper = $documentationMapper;
    }

    /**
     * @param Shipment $apiShipment
     * @return ShipmentInterface
     */
    private function mapShipment(Shipment $apiShipment)
    {
        $shipment = $this->shipmentFactory->create(['data' => [
            ShipmentInterface::SHIPMENT_ID => $apiShipment->getId(),
            ShipmentInterface::STATUS => $apiShipment->getStatus(),
            ShipmentInterface::MESSAGE => $apiShipment->getMessage(),
        ]]);

        return $shipment;
    }

    /**
     * @param CompletionResponseType $apiCompletion
     * @return DispatchInterface
     */
    public function map(CompletionResponseType $apiCompletion)
    {
        $dispatchId = $apiCompletion->getId();
        $status = $apiCompletion->getAttributes()->getStatus();
        $customAttributes = $apiCompletion->getAttributes()->getCustomAttributes();
        $carrierName = $customAttributes ? $customAttributes->getCarrierName() : 'No Carrier Found' ;
        $createdAtDate = $apiCompletion->getAttributes()->getCreatedAt();
        $readyAtDate = $apiCompletion->getAttributes()->getReadyAt();

        $documentationCollection = $this->documentationCollectionFactory->create();
        foreach ($apiCompletion->getAttributes()->getGroups() as $attributeGroup) {
            foreach ($attributeGroup->getDocumentation() as $apiDoc) {
                $documentation = $this->documentationMapper->map($apiDoc);
                $documentationCollection->offsetSet($documentation->getDocumentationId(), $documentation);
            }
        }

        $failedShipments = [];
        $includedShipments = [];
        foreach ($apiCompletion->getAttributes()->getShipments() as $apiShipment) {
            if ($apiShipment->getStatus() === 'error') {
                $failedShipments[$apiShipment->getId()] = $this->mapShipment($apiShipment);
            } else {
                $includedShipments[$apiShipment->getId()] = $this->mapShipment($apiShipment);
            }
        }

        $dispatch = $this->dispatchFactory->create(['data' => [
            DispatchInterface::DISPATCH_ID => $dispatchId,
            DispatchInterface::STATUS => $status,
            DispatchInterface::CARRIER_NAME => $carrierName,
            DispatchInterface::CREATED_AT_DATE => $createdAtDate,
            DispatchInterface::READY_AT_DATE => $readyAtDate,
            DispatchInterface::INCLUDED_SHIPMENTS => $includedShipments,
            DispatchInterface::FAILED_SHIPMENTS => $failedShipments,
            DispatchInterface::DOCUMENTATION => $documentationCollection,
        ]]);

        return $dispatch;
    }
}
