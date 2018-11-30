<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

/**
 * Temando Shipment Reference Repository Interface.
 *
 * A shipment entity is registered at the Temando platform in order to create
 * shipping labels and other documentation. A reference to the external shipment
 * is stored locally.
 *
 * This interface can be used to create/read/update the local reference.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface ShipmentReferenceRepositoryInterface
{
    /**
     * Load local track info.
     *
     * @param string $trackingNumber
     * @param string $carrierCode
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getShipmentTrack($trackingNumber, $carrierCode);

    /**
     * Save local reference to external shipment entity.
     *
     * @param \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface $shipment
     * @return \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface $shipment);

    /**
     * Load local reference to external shipment entity.
     *
     * @param int $entityId
     * @return \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);

    /**
     * Load local reference to external shipment entity by Magento shipment ID.
     *
     * @param int $shipmentId
     * @return \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByShipmentId($shipmentId);

    /**
     * Load local reference to external shipment entity by Temando shipment ID.
     *
     * @param string $extShipmentId
     * @return \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByExtShipmentId($extShipmentId);

    /**
     * Load local reference to external shipment entity by Temando return shipment ID.
     *
     * @param string $extShipmentId
     *
     * @return \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByExtReturnShipmentId($extShipmentId);

    /**
     * Load local reference to external shipment entity by tracking number.
     *
     * @param string $trackingNumber
     * @return \Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByTrackingNumber($trackingNumber);

    /**
     * List shipment references that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Temando\Shipping\Model\ResourceModel\Shipment\ShipmentReferenceCollection
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
