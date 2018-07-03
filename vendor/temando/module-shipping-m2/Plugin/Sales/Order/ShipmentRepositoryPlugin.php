<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Sales\Order;

use Magento\Sales\Api\Data\ShipmentExtensionFactory;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface as SalesShipmentRepositoryInterface;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;

/**
 * Save and load external shipment reference to/from shipment
 *
 * @package  Temando\Shipping\Plugin
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentRepositoryPlugin
{
    /**
     * @var ShipmentExtensionFactory
     */
    private $shipmentExtensionFactory;

    /**
     * @var ShipmentReferenceInterfaceFactory
     */
    private $shipmentReferenceFactory;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * ShipmentRepositoryPlugin constructor.
     * @param ShipmentExtensionFactory $extensionFactory
     * @param ShipmentReferenceInterfaceFactory $shipmentReferenceFactory
     * @param ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        ShipmentExtensionFactory $extensionFactory,
        ShipmentReferenceInterfaceFactory $shipmentReferenceFactory,
        ShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->shipmentExtensionFactory = $extensionFactory;
        $this->shipmentReferenceFactory = $shipmentReferenceFactory;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Load extension attributes to shipment.
     *
     * @param SalesShipmentRepositoryInterface $subject
     * @param ShipmentInterface $shipment
     * @return ShipmentInterface
     */
    public function afterGet(
        SalesShipmentRepositoryInterface $subject,
        ShipmentInterface $shipment
    ) {
        $extensionAttributes = $shipment->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->shipmentExtensionFactory->create();
        }

        try {
            /** @var \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface $extShipment */
            $extShipment = $this->shipmentRepository->getReferenceByShipmentId($shipment->getEntityId());
            $extensionAttributes->setExtShipmentId($extShipment->getExtShipmentId());
            $extensionAttributes->setExtReturnShipmentId($extShipment->getExtReturnShipmentId());
            $extensionAttributes->setExtLocationId($extShipment->getExtLocationId());
            $extensionAttributes->setExtTrackingUrl($extShipment->getExtTrackingUrl());
            $extensionAttributes->setExtTrackingReference($extShipment->getExtTrackingReference());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $extensionAttributes->setExtShipmentId(null);
            $extensionAttributes->setExtLocationId(null);
            $extensionAttributes->setExtTrackingUrl(null);
            $extensionAttributes->setExtTrackingReference(null);
        }

        $shipment->setExtensionAttributes($extensionAttributes);

        return $shipment;
    }

    /**
     * When new shipments are created through the salesShipOrderV1 service,
     * additional data may be passed in as extension attributes, e.g. reference
     * IDs to external entities at the Temando platform.
     * These additional attributes need to be persisted in local storage.
     *
     * @see \Magento\Sales\Api\ShipOrderInterface::execute
     * @see ShipmentReferenceInterface
     *
     * @param SalesShipmentRepositoryInterface $subject
     * @param callable $proceed
     * @param ShipmentInterface|\Magento\Sales\Model\Order\Shipment $shipment
     * @return ShipmentInterface
     */
    public function aroundSave(
        SalesShipmentRepositoryInterface $subject,
        callable $proceed,
        ShipmentInterface $shipment
    ) {
        $saveShipmentReference = $shipment->isObjectNew();

        /** @var ShipmentInterface $shipment */
        $shipment = $proceed($shipment);

        $extensionAttributes = $shipment->getExtensionAttributes();
        if ($saveShipmentReference && ($extensionAttributes !== null)) {
            /** @var \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface $shipmentReference */
            $shipmentReference = $this->shipmentReferenceFactory->create();
            $shipmentReference->setShipmentId($shipment->getEntityId());
            $shipmentReference->setExtShipmentId($extensionAttributes->getExtShipmentId());
            $shipmentReference->setExtReturnShipmentId($extensionAttributes->getExtReturnShipmentId());
            $shipmentReference->setExtLocationId($extensionAttributes->getExtLocationId());
            $shipmentReference->setExtTrackingUrl($extensionAttributes->getExtTrackingUrl());
            $shipmentReference->setExtTrackingReference($extensionAttributes->getExtTrackingReference());

            $this->shipmentRepository->saveReference($shipmentReference);
        }

        return $shipment;
    }
}
