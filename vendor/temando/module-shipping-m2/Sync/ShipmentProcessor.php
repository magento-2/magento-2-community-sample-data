<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Sync;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\ShipmentCreationArgumentsExtensionFactory;
use Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface;
use Magento\Sales\Api\Data\ShipmentTrackCreationInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory;
use Magento\Sales\Api\ShipOrderInterface;
use Temando\Shipping\Model\StreamEventInterface;
use Temando\Shipping\Sync\Exception\EventException;
use Temando\Shipping\Sync\Exception\EventProcessorException;
use Temando\Shipping\Model\ResourceModel\Order\OrderReference as OrderReferenceResource;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Temando Shipment Event Processor
 *
 * @package  Temando\Shipping\Sync
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentProcessor implements EntityProcessorInterface
{
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    private $salesShipmentRepository;

    /**
     * @var ShipmentCreationArgumentsInterface
     */
    private $shipmentCreationArguments;

    /**
     * @var ShipOrderInterface
     */
    private $shipOrder;

    /**
     * @var OrderReferenceResource
     */
    private $orderReferenceResource;

    /**
     * @var ShipmentTrackCreationInterfaceFactory
     */
    private $trackCreationFactory;

    /**
     * @var ShipmentTrackInterfaceFactory
     */
    private $trackUpdateFactory;

    /**
     * @var ShipmentCreationArgumentsExtensionFactory
     */
    private $shipmentExtensionFactory;

    /**
     * ShipmentEventProcessor constructor.
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $salesShipmentRepository
     * @param ShipmentCreationArgumentsInterface $shipmentCreationArguments
     * @param ShipOrderInterface $shipOrder
     * @param OrderReferenceResource $orderReferenceResource
     * @param ShipmentTrackCreationInterfaceFactory $trackCreationFactory
     * @param ShipmentTrackInterfaceFactory $trackUpdateFactory
     * @param ShipmentCreationArgumentsExtensionFactory $shipmentExtensionFactory
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Api\ShipmentRepositoryInterface $salesShipmentRepository,
        ShipmentCreationArgumentsInterface $shipmentCreationArguments,
        ShipOrderInterface $shipOrder,
        OrderReferenceResource $orderReferenceResource,
        ShipmentTrackCreationInterfaceFactory $trackCreationFactory,
        ShipmentTrackInterfaceFactory $trackUpdateFactory,
        ShipmentCreationArgumentsExtensionFactory $shipmentExtensionFactory
    ) {
        $this->shipmentRepository        = $shipmentRepository;
        $this->salesShipmentRepository   = $salesShipmentRepository;
        $this->shipmentCreationArguments = $shipmentCreationArguments;
        $this->shipOrder                 = $shipOrder;
        $this->orderReferenceResource    = $orderReferenceResource;
        $this->trackCreationFactory      = $trackCreationFactory;
        $this->trackUpdateFactory        = $trackUpdateFactory;
        $this->shipmentExtensionFactory  = $shipmentExtensionFactory;
    }

    /**
     * @param string $extShipmentId
     * @return bool
     */
    private function isShipmentNew($extShipmentId)
    {
        try {
            $this->shipmentRepository->getReferenceByExtShipmentId($extShipmentId);
            return false;
        } catch (NoSuchEntityException $e) {
            return true;
        }
    }

    /**
     * @param string $extShipmentId
     * @return \Temando\Shipping\Model\ShipmentInterface
     * @throws EventProcessorException
     */
    private function loadExternalShipment($extShipmentId)
    {
        try {
            // load external shipment
            /** @var \Temando\Shipping\Model\Shipment $shipment */
            return $this->shipmentRepository->getById($extShipmentId);
        } catch (LocalizedException $e) {
            throw EventProcessorException::processingFailed(
                StreamEventInterface::ENTITY_TYPE_SHIPMENT,
                $extShipmentId,
                $e
            );
        }
    }

    /**
     * Create new shipment
     *
     * @param string $extShipmentId
     * @return int Processed entity ID.
     * @throws EventException
     * @throws EventProcessorException
     */
    private function create($extShipmentId)
    {
        $shipment = $this->loadExternalShipment($extShipmentId);

        // skip shipment event if no fulfillment with tracking number is available
        $fulfillment = $shipment->getFulfillment();
        if (!$fulfillment || !$fulfillment->getTrackingReference()) {
            throw EventException::operationSkipped(
                StreamEventInterface::ENTITY_TYPE_SHIPMENT,
                StreamEventInterface::EVENT_TYPE_CREATE,
                $extShipmentId,
                'No fulfillment information available.'
            );
        }

        // find local order id for external order
        $orderId = $this->orderReferenceResource->getOrderIdByExtOrderId($shipment->getOrderId());
        if (!$orderId) {
            throw EventException::operationSkipped(
                StreamEventInterface::ENTITY_TYPE_SHIPMENT,
                StreamEventInterface::EVENT_TYPE_CREATE,
                $extShipmentId,
                "No order found for entity ID {$shipment->getOrderId()}."
            );
        }

        $tracking = $this->trackCreationFactory->create();
        $tracking->setCarrierCode(Carrier::CODE);
        $tracking->setTitle($fulfillment->getServiceName());
        $tracking->setTrackNumber($fulfillment->getTrackingReference());

        $extensionAttributes = $this->shipmentExtensionFactory->create();
        $extensionAttributes->setExtLocationId($shipment->getOriginId());
        $extensionAttributes->setExtShipmentId($extShipmentId);
        $extensionAttributes->setExtTrackingReference($fulfillment->getTrackingReference());

        $arguments = $this->shipmentCreationArguments->setExtensionAttributes($extensionAttributes);

        try {
            $shipmentId = $this->shipOrder->execute(
                $orderId,
                [], // items information is not available at the API
                true, // Notify the customer (tracking email)
                false, // add comment
                null,
                [$tracking],
                [], // Package information is not available at the API
                $arguments
            );
        } catch (LocalizedException $e) {
            throw EventProcessorException::processingFailed(
                StreamEventInterface::ENTITY_TYPE_SHIPMENT,
                $extShipmentId,
                $e
            );
        }

        return $shipmentId;
    }

    /**
     * Update existing shipment, i.e. add tracking information
     *
     * @param string $extShipmentId
     * @return int Processed entity ID.
     * @throws EventException
     */
    private function modify($extShipmentId)
    {
        if ($this->isShipmentNew($extShipmentId)) {
            return $this->create($extShipmentId);
        }

        $shipment = $this->loadExternalShipment($extShipmentId);

        // skip shipment event if no fulfillment with tracking number is available
        $fulfillment = $shipment->getFulfillment();
        if (!$fulfillment || !$fulfillment->getTrackingReference()) {
            throw EventException::operationSkipped(
                StreamEventInterface::ENTITY_TYPE_SHIPMENT,
                StreamEventInterface::EVENT_TYPE_CREATE,
                $extShipmentId,
                'No fulfillment information available.'
            );
        }

        $shipmentReference = $this->shipmentRepository->getReferenceByExtShipmentId($extShipmentId);
        $salesShipmentId = $shipmentReference->getShipmentId();

        /** @var \Magento\Sales\Model\Order\Shipment $salesShipment */
        $salesShipment = $this->salesShipmentRepository->get($salesShipmentId);

        /** @var  $tracks */
        $tracks = $salesShipment->getTracksCollection();
        foreach ($tracks as $track) {
            if ($track->getTrackNumber() === $fulfillment->getTrackingReference()) {
                // tracking number already exists, nothing to do.
                return $salesShipmentId;
            }
        }

        /** @var \Magento\Sales\Model\Order\Shipment\Track $tracking */
        $tracking = $this->trackUpdateFactory->create();
        $tracking->setCarrierCode(Carrier::CODE);
        $tracking->setTitle($fulfillment->getServiceName());
        $tracking->setTrackNumber($fulfillment->getTrackingReference());

        $salesShipment->addTrack($tracking);
        $this->salesShipmentRepository->save($salesShipment);

        return $salesShipmentId;
    }

    /**
     * @param string $operation
     * @param string $extShipmentId
     * @return int Processed entity ID.
     * @throws EventException
     * @throws EventProcessorException
     */
    public function execute($operation, $extShipmentId)
    {
        if ($operation == StreamEventInterface::EVENT_TYPE_MODIFY) {
            return $this->modify($extShipmentId);
        }

        if ($operation == StreamEventInterface::EVENT_TYPE_CREATE) {
            return $this->create($extShipmentId);
        }

        throw EventException::unknownOperation(
            StreamEventInterface::ENTITY_TYPE_SHIPMENT,
            $operation
        );
    }
}
