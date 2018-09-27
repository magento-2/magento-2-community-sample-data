<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\Shipment;

/**
 * Shipment Reference Interface.
 *
 * A shipment reference represents the link between local shipment and
 * a shipment entity at the Temando platform.
 *
 * @api
 * @package  Temando\Shipping\Api
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ShipmentReferenceInterface
{
    const ENTITY_ID              = 'entity_id';
    const SHIPMENT_ID            = 'shipment_id';
    const EXT_SHIPMENT_ID        = 'ext_shipment_id';
    const EXT_RETURN_SHIPMENT_ID = 'ext_return_shipment_id';
    const EXT_LOCATION_ID        = 'ext_location_id';
    const EXT_TRACKING_URL       = 'ext_tracking_url';
    const EXT_TRACKING_REFERENCE = 'ext_tracking_reference';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return void
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getShipmentId();

    /**
     * @param int $shipmentId
     * @return void
     */
    public function setShipmentId($shipmentId);

    /**
     * @return string
     */
    public function getExtShipmentId();

    /**
     * @param string $extShipmentId
     * @return void
     */
    public function setExtShipmentId($extShipmentId);

    /**
     * @return string
     */
    public function getExtReturnShipmentId();

    /**
     * @param string $extReturnShipmentId
     * @return void
     */
    public function setExtReturnShipmentId($extReturnShipmentId);

    /**
     * @return string
     */
    public function getExtLocationId();

    /**
     * @param string $extLocationId
     * @return void
     */
    public function setExtLocationId($extLocationId);

    /**
     * @return string
     */
    public function getExtTrackingUrl();

    /**
     * @param string $extTrackingUrl
     * @return void
     */
    public function setExtTrackingUrl($extTrackingUrl);

    /**
     * @return string
     */
    public function getExtTrackingReference();

    /**
     * @param string $extTrackingReference
     * @return void
     */
    public function setExtTrackingReference($extTrackingReference);
}
