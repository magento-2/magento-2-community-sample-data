<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order\AutoProcessing;

use Temando\Shipping\Model\Shipment\FulfillmentInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando Order Fulfillment Shipment Filter.
 *
 * Extract shipments from AllocateOrder API response.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentFilter
{
    /**
     * @param ShipmentInterface[] $shipments
     * @return ShipmentInterface[]
     */
    public function getFulfilledShipments(array $shipments)
    {
        $fulfilledShipments = array_filter($shipments, function (ShipmentInterface $shipment) {
            // skip shipments that were not yet fulfilled
            if (!$shipment->getFulfillment() instanceof FulfillmentInterface) {
                return false;
            }

            $capabilities = $shipment->getCapabilities();
            foreach ($capabilities as $capability) {
                if ($capability->getCapabilityId() === 'return') {
                    // skip return shipments
                    return false;
                }
            }

            return true;
        });

        return array_values($fulfilledShipments);
    }

    /**
     * @param ShipmentInterface[] $shipments
     * @return ShipmentInterface[]
     */
    public function getReturnShipments(array $shipments)
    {
        $returnShipments = array_filter($shipments, function (ShipmentInterface $shipment) {
            $capabilities = $shipment->getCapabilities();
            foreach ($capabilities as $capability) {
                if ($capability->getCapabilityId() === 'return') {
                    // only return shipments
                    return true;
                }
            }

            return false;
        });

        return array_values($returnShipments);
    }
}
