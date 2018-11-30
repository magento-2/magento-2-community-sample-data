<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order\AutoProcessing;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentCommentCreationInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentCreationArgumentsExtensionInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentCreationArgumentsInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentItemCreationInterface;
use Magento\Sales\Api\Data\ShipmentItemCreationInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentTrackCreationInterfaceFactory;
use Magento\Sales\Api\ShipOrderInterface;
use Temando\Shipping\Model\DocumentationInterface;
use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\Shipment\PackageItemInterface;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Order Fulfillment Processor.
 *
 * Create shipments locally based on AllocateOrder API response.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AutoFulfill implements AutoFulfillInterface
{
    /**
     * @var OrderStatusHistoryUpdater
     */
    private $historyUpdater;

    /**
     * @var ShipmentFilter
     */
    private $shipmentFilter;

    /**
     * @var ShipmentItemCreationInterfaceFactory
     */
    private $itemFactory;

    /**
     * @var ShipmentCommentCreationInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var ShipmentTrackCreationInterfaceFactory
     */
    private $trackCreationFactory;

    /**
     * @var ShipmentCreationArgumentsInterfaceFactory
     */
    private $shipmentCreationArgumentsFactory;

    /**
     * @var ShipmentCreationArgumentsExtensionInterfaceFactory
     */
    private $shipmentCreationArgumentsExtensionFactory;

    /**
     * @var ShipOrderInterface
     */
    private $shipOrder;

    /**
     * AutoFulfill constructor.
     * @param OrderStatusHistoryUpdater $historyUpdater
     * @param ShipmentFilter $shipmentFilter
     * @param ShipmentItemCreationInterfaceFactory $itemFactory
     * @param ShipmentCommentCreationInterfaceFactory $commentFactory
     * @param ShipmentTrackCreationInterfaceFactory $trackCreationFactory
     * @param ShipmentCreationArgumentsInterfaceFactory $shipmentCreationArgumentsFactory
     * @param ShipmentCreationArgumentsExtensionInterfaceFactory $shipmentCreationArgumentsExtensionFactory
     * @param ShipOrderInterface $shipOrder
     */
    public function __construct(
        OrderStatusHistoryUpdater $historyUpdater,
        ShipmentFilter $shipmentFilter,
        ShipmentItemCreationInterfaceFactory $itemFactory,
        ShipmentCommentCreationInterfaceFactory $commentFactory,
        ShipmentTrackCreationInterfaceFactory $trackCreationFactory,
        ShipmentCreationArgumentsInterfaceFactory $shipmentCreationArgumentsFactory,
        ShipmentCreationArgumentsExtensionInterfaceFactory $shipmentCreationArgumentsExtensionFactory,
        ShipOrderInterface $shipOrder
    ) {
        $this->historyUpdater = $historyUpdater;
        $this->shipmentFilter = $shipmentFilter;
        $this->itemFactory = $itemFactory;
        $this->commentFactory = $commentFactory;
        $this->trackCreationFactory = $trackCreationFactory;
        $this->shipmentCreationArgumentsFactory = $shipmentCreationArgumentsFactory;
        $this->shipmentCreationArgumentsExtensionFactory = $shipmentCreationArgumentsExtensionFactory;
        $this->shipOrder = $shipOrder;
    }

    /**
     * @param OrderInterface $salesOrder
     * @param string $sku
     * @return int|null
     */
    private function getOrderItemIdBySku(OrderInterface $salesOrder, $sku)
    {
        foreach ($salesOrder->getItems() as $item) {
            if ($item->getSku() === $sku) {
                return $item->getItemId();
            }
        }

        return null;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $salesOrder
     * @param \Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface $orderResponse
     * @return int[]
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createShipments(OrderInterface $salesOrder, OrderResponseTypeInterface $orderResponse)
    {
        if ($orderResponse->getErrors()) {
            $this->historyUpdater->addErrors($salesOrder, $orderResponse->getErrors());
            return [];
        }

        $shipments = (array)$orderResponse->getShipments();
        $shipmentIds = [];

        $fulfilledShipments = $this->shipmentFilter->getFulfilledShipments($shipments);
        $returnShipments = $this->shipmentFilter->getReturnShipments($shipments);

        /** @var \Temando\Shipping\Model\ShipmentInterface $fulfilledShipment */
        foreach ($fulfilledShipments as $index => $fulfilledShipment) {
            // items
            $creationItems = [];

            $packages = $fulfilledShipment->getPackages() ?: [];

            /** @var PackageItemInterface[] $fulfilledItems */
            $fulfilledItems = array_reduce($packages, function (array $items, PackageInterface $package) {
                $items = array_merge($items, $package->getItems());
                return $items;
            }, []);

            foreach ($fulfilledItems as $fulfilledItem) {
                $orderItemId = $this->getOrderItemIdBySku($salesOrder, $fulfilledItem->getSku());

                /** @var ShipmentItemCreationInterface $item */
                $creationItem = $this->itemFactory->create();
                $creationItem->setQty($fulfilledItem->getQty());
                $creationItem->setOrderItemId($orderItemId);
                $creationItems[]= $creationItem;
            }

            // tracking
            $fulfillment = $fulfilledShipment->getFulfillment();
            $tracking = $this->trackCreationFactory->create();
            $tracking->setCarrierCode(Carrier::CODE);
            $tracking->setTitle($fulfillment->getServiceName());
            $tracking->setTrackNumber($fulfillment->getTrackingReference());

            // remote references and shipping label
            /** @var DocumentationInterface $documentation */
            $documentation = current($fulfilledShipment->getDocumentation());
            $labelUrl = empty($documentation) ? '' : $documentation->getUrl();

            $extensionAttributes = $this->shipmentCreationArgumentsExtensionFactory->create();
            $extensionAttributes->setExtLocationId($fulfilledShipment->getOriginId());
            $extensionAttributes->setExtShipmentId($fulfilledShipment->getShipmentId());
            $extensionAttributes->setExtTrackingReference($fulfilledShipment->getFulfillment()->getTrackingReference());
            $extensionAttributes->setShippingLabel($labelUrl);

            if (isset($returnShipments[$index])) {
                // add forward fulfillment return shipment ID
                $extensionAttributes->setExtReturnShipmentId($returnShipments[$index]->getShipmentId());
            }

            $arguments = $this->shipmentCreationArgumentsFactory->create();
            $arguments->setExtensionAttributes($extensionAttributes);

            // comment
            $comment = $this->commentFactory->create();
            $comment->setComment('This shipment was automatically created with Magento Shipping.');

            $shipmentIds[]= $this->shipOrder->execute(
                $salesOrder->getEntityId(),
                $creationItems,
                false,
                true,
                $comment,
                [$tracking],
                [],
                $arguments
            );
        }

        return $shipmentIds;
    }
}
