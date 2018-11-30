<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

/**
 * Temando Shipment Repository Interface.
 *
 * A shipment entity is registered at the Temando platform in order to create
 * shipping labels and other documentation. A reference to the external shipment
 * is stored locally.
 *
 * This interface can be used to retrieve shipment details and tracking
 * information from the Temando platform.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface ShipmentRepositoryInterface
{
    /**
     * Load external shipment entity from platform.
     *
     * @param string $shipmentId
     * @return \Temando\Shipping\Model\ShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($shipmentId);

    /**
     * Load external tracking info from platform using external shipment id.
     *
     * @param string $shipmentId
     * @return \Temando\Shipping\Model\Shipment\TrackEventInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTrackingById($shipmentId);

    /**
     * Load external tracking info from platform using tracking number.
     *
     * @param string $trackingNumber
     * @return \Temando\Shipping\Model\Shipment\TrackEventInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTrackingByNumber($trackingNumber);
}
