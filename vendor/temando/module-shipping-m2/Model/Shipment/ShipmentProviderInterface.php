<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Shipment;

use Magento\Sales\Api\Data\ShipmentInterface as SalesShipmentInterface;
use Temando\Shipping\Model\ShipmentInterface as ShipmentInterface;

/**
 * Temando Shipment Provider Interface.
 *
 * A track represents a complete tracking status history.
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ShipmentProviderInterface
{
    /**
     * @return ShipmentInterface
     */
    public function getShipment();

    /**
     * @param ShipmentInterface $shipment
     *
     * @return void
     */
    public function setShipment(ShipmentInterface $shipment);

    /**
     * @return SalesShipmentInterface
     */
    public function getSalesShipment();

    /**
     * @param SalesShipmentInterface $shipment
     *
     * @return void
     */
    public function setSalesShipment(SalesShipmentInterface $shipment);
}
