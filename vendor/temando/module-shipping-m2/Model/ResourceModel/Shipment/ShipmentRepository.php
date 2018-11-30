<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Shipment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track as TrackResource;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Shipment\ShipmentReference as ShipmentReferenceResource;
use Temando\Shipping\Model\Shipment\TrackEventInterface;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Rest\Adapter\ShipmentApiInterface;
use Temando\Shipping\Rest\EntityMapper\ShipmentResponseMapper;
use Temando\Shipping\Rest\EntityMapper\TrackingResponseMapper;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\TrackingEvent;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Shipment Repository
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class ShipmentRepository implements ShipmentRepositoryInterface
{
    /**
     * @var ShipmentApiInterface
     */
    private $apiAdapter;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $requestFactory;

    /**
     * @var ShipmentResponseMapper
     */
    private $shipmentMapper;

    /**
     * @var TrackingResponseMapper
     */
    private $trackMapper;

    /**
     * @var ShipmentReferenceResource
     */
    private $resource;

    /**
     * @var TrackResource
     */
    private $trackResource;

    /**
     * ShipmentRepository constructor.
     * @param ShipmentApiInterface $apiAdapter
     * @param ItemRequestInterfaceFactory $requestFactory
     * @param ShipmentResponseMapper $shipmentMapper
     * @param TrackingResponseMapper $trackMapper
     * @param ShipmentReference $resource
     * @param TrackResource $trackResource
     */
    public function __construct(
        ShipmentApiInterface $apiAdapter,
        ItemRequestInterfaceFactory $requestFactory,
        ShipmentResponseMapper $shipmentMapper,
        TrackingResponseMapper $trackMapper,
        ShipmentReferenceResource $resource,
        TrackResource $trackResource
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->requestFactory = $requestFactory;
        $this->shipmentMapper = $shipmentMapper;
        $this->trackMapper = $trackMapper;
        $this->resource = $resource;
        $this->trackResource = $trackResource;
    }

    /**
     * Load external shipment entity from platform.
     *
     * @param string $shipmentId
     * @return ShipmentInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getById($shipmentId)
    {
        if (!$shipmentId) {
            throw new LocalizedException(__('An error occurred while loading data.'));
        }

        try {
            $request = $this->requestFactory->create(['entityId' => $shipmentId]);
            $apiShipment = $this->apiAdapter->getShipment($request);
            $shipment = $this->shipmentMapper->map($apiShipment);
        } catch (AdapterException $e) {
            if ($e->getCode() === 404) {
                throw NoSuchEntityException::singleField('shipmentId', $shipmentId);
            }

            throw new LocalizedException(__('An error occurred while loading data.'), $e);
        }

        return $shipment;
    }

    /**
     * Load external tracking info from platform using external shipment id.
     *
     * @param string $shipmentId
     * @return TrackEventInterface[]
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getTrackingById($shipmentId)
    {
        try {
            $request = $this->requestFactory->create(['entityId' => $shipmentId]);
            $apiTrackingEvents = $this->apiAdapter->getTrackingEvents($request);

            // Sort the tracking events by occurredAt descending.
            usort($apiTrackingEvents, function (TrackingEvent $eventA, TrackingEvent $eventB) {
                $occurredA = strtotime($eventA->getAttributes()->getOccurredAt());
                $occurredB = strtotime($eventB->getAttributes()->getOccurredAt());
                return ($occurredB - $occurredA);
            });

            $trackEvents = array_map(function (TrackingEvent $apiTrackingEvent) {
                return $this->trackMapper->map($apiTrackingEvent);
            }, $apiTrackingEvents);
        } catch (AdapterException $e) {
            if ($e->getCode() === 404) {
                throw NoSuchEntityException::singleField('shipmentId', $shipmentId);
            }

            throw new LocalizedException(__('An error occurred while loading tracking history.'), $e);
        }

        return $trackEvents;
    }

    /**
     * Load external tracking info from platform using tracking number.
     *
     * @param string $trackingNumber
     * @return TrackEventInterface[]
     * @throws NoSuchEntityException
     */
    public function getTrackingByNumber($trackingNumber)
    {
        $connection = $this->resource->getConnection();
        $select = $connection
            ->select()
            ->from(['ts' => SetupSchema::TABLE_SHIPMENT], ShipmentReferenceInterface::EXT_SHIPMENT_ID)
            ->join(['sst' => $this->trackResource->getMainTable()], 'ts.shipment_id = sst.parent_id')
            ->where('sst.track_number = ?', $trackingNumber);

        $shipmentId = $connection->fetchOne($select);

        $trackEvents = $this->getTrackingById($shipmentId);
        $trackEvents = array_filter($trackEvents, function (TrackEventInterface $trackEvent) use ($trackingNumber) {
            return ($trackingNumber === $trackEvent->getTrackingReference());
        });

        return $trackEvents;
    }
}
