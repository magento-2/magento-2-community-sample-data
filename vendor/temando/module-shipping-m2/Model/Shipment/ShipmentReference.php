<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\Model\AbstractModel;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Model\ResourceModel\Shipment\ShipmentReference as ShipmentResource;

/**
 * Reference to shipment entity created at Temando platform
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentReference extends AbstractModel implements ShipmentReferenceInterface
{
    /**
     * Init resource model.
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ShipmentResource::class);
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(ShipmentReferenceInterface::ENTITY_ID);
    }

    /**
     * @param int $entityId
     * @return void
     */
    public function setEntityId($entityId)
    {
        $this->setData(ShipmentReferenceInterface::ENTITY_ID, $entityId);
    }

    /**
     * @return int
     */
    public function getShipmentId()
    {
        return $this->getData(ShipmentReferenceInterface::SHIPMENT_ID);
    }

    /**
     * @param int $shipmentId
     * @return void
     */
    public function setShipmentId($shipmentId)
    {
        $this->setData(ShipmentReferenceInterface::SHIPMENT_ID, $shipmentId);
    }

    /**
     * @return string
     */
    public function getExtShipmentId()
    {
        return $this->getData(ShipmentReferenceInterface::EXT_SHIPMENT_ID);
    }

    /**
     * @param string $extShipmentId
     * @return void
     */
    public function setExtShipmentId($extShipmentId)
    {
        $this->setData(ShipmentReferenceInterface::EXT_SHIPMENT_ID, $extShipmentId);
    }

    /**
     * @return string
     */
    public function getExtReturnShipmentId()
    {
        return $this->getData(ShipmentReferenceInterface::EXT_RETURN_SHIPMENT_ID);
    }

    /**
     * @param string $extReturnShipmentId
     * @return void
     */
    public function setExtReturnShipmentId($extReturnShipmentId)
    {
        $this->setData(ShipmentReferenceInterface::EXT_RETURN_SHIPMENT_ID, $extReturnShipmentId);
    }

    /**
     * @return string
     */
    public function getExtLocationId()
    {
        return $this->getData(ShipmentReferenceInterface::EXT_LOCATION_ID);
    }

    /**
     * @param string $extLocationId
     * @return void
     */
    public function setExtLocationId($extLocationId)
    {
        $this->setData(ShipmentReferenceInterface::EXT_LOCATION_ID, $extLocationId);
    }

    /**
     * @return string
     */
    public function getExtTrackingUrl()
    {
        return $this->getData(ShipmentReferenceInterface::EXT_TRACKING_URL);
    }

    /**
     * @param string $extTrackingUrl
     * @return void
     */
    public function setExtTrackingUrl($extTrackingUrl)
    {
        $this->setData(ShipmentReferenceInterface::EXT_TRACKING_URL, $extTrackingUrl);
    }

    /**
     * @return string
     */
    public function getExtTrackingReference()
    {
        return $this->getData(ShipmentReferenceInterface::EXT_TRACKING_REFERENCE);
    }

    /**
     * @param string $extTrackingReference
     * @return void
     */
    public function setExtTrackingReference($extTrackingReference)
    {
        $this->setData(ShipmentReferenceInterface::EXT_TRACKING_REFERENCE, $extTrackingReference);
    }
}
