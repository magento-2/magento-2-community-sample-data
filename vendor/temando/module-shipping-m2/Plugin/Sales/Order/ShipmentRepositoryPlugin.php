<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Sales\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentExtensionFactory;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentReferenceRepositoryInterface;

/**
 * Save and load external shipment reference to/from shipment
 *
 * @package Temando\Shipping\Plugin
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
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
     * @var ShipmentReferenceRepositoryInterface
     */
    private $shipmentReferenceRepository;

    /**
     * ShipmentRepositoryPlugin constructor.
     * @param ShipmentExtensionFactory $extensionFactory
     * @param ShipmentReferenceInterfaceFactory $shipmentReferenceFactory
     * @param ShipmentReferenceRepositoryInterface $shipmentReferenceRepository
     */
    public function __construct(
        ShipmentExtensionFactory $extensionFactory,
        ShipmentReferenceInterfaceFactory $shipmentReferenceFactory,
        ShipmentReferenceRepositoryInterface $shipmentReferenceRepository
    ) {
        $this->shipmentExtensionFactory = $extensionFactory;
        $this->shipmentReferenceFactory = $shipmentReferenceFactory;
        $this->shipmentReferenceRepository = $shipmentReferenceRepository;
    }

    /**
     * Load extension attributes to shipment.
     *
     * @param ShipmentRepositoryInterface $subject
     * @param ShipmentInterface $shipment
     * @return ShipmentInterface
     */
    public function afterGet(
        ShipmentRepositoryInterface $subject,
        ShipmentInterface $shipment
    ) {
        $extensionAttributes = $shipment->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->shipmentExtensionFactory->create();
        }

        try {
            /** @var \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface $extShipment */
            $extShipment = $this->shipmentReferenceRepository->getByShipmentId($shipment->getEntityId());
            $extensionAttributes->setExtShipmentId($extShipment->getExtShipmentId());
            $extensionAttributes->setExtReturnShipmentId($extShipment->getExtReturnShipmentId());
            $extensionAttributes->setExtLocationId($extShipment->getExtLocationId());
            $extensionAttributes->setExtTrackingUrl($extShipment->getExtTrackingUrl());
            $extensionAttributes->setExtTrackingReference($extShipment->getExtTrackingReference());
        } catch (LocalizedException $e) {
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
     * @param ShipmentRepositoryInterface $subject
     * @param callable $proceed
     * @param ShipmentInterface|\Magento\Sales\Model\Order\Shipment $shipment
     * @return ShipmentInterface
     */
    public function aroundSave(
        ShipmentRepositoryInterface $subject,
        callable $proceed,
        ShipmentInterface $shipment
    ) {
        $canSave = $shipment->isObjectNew();

        /** @var ShipmentInterface $shipment */
        $shipment = $proceed($shipment);
        $extensionAttributes = $shipment->getExtensionAttributes();

        $canSave = $canSave && $extensionAttributes && $extensionAttributes->getExtShipmentId();
        if ($canSave) {
            /** @var \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface $shipmentReference */
            $shipmentReference = $this->shipmentReferenceFactory->create();
            $shipmentReference->setShipmentId($shipment->getEntityId());
            $shipmentReference->setExtShipmentId($extensionAttributes->getExtShipmentId());
            $shipmentReference->setExtReturnShipmentId($extensionAttributes->getExtReturnShipmentId());
            $shipmentReference->setExtLocationId($extensionAttributes->getExtLocationId());
            $shipmentReference->setExtTrackingUrl($extensionAttributes->getExtTrackingUrl());
            $shipmentReference->setExtTrackingReference($extensionAttributes->getExtTrackingReference());

            $this->shipmentReferenceRepository->save($shipmentReference);
        }

        return $shipment;
    }
}
