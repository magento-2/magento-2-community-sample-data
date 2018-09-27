<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Order Fulfillment Interface.
 *
 * When we import external. When shipment details are requested from the API the
 * response also contains this shipment origin data object.
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface FulfillmentInterface
{
    const SERVICE_NAME = 'service_name';
    const TRACKING_REFERENCE = 'tracking_reference';
    const CARRIER_NAME = 'carrier_name';

    /**
     * Get readable label for shipment method.
     *
     * @return string
     */
    public function getServiceName();

    /**
     * Get Tracking Number for this shipment.
     *
     * @return string
     */
    public function getTrackingReference();

    /**
     * Get carrier name.
     *
     * @return string
     */
    public function getCarrierName();
}
