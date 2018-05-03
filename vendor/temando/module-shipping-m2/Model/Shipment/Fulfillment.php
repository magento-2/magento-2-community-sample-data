<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\DataObject;

/**
 * Temando Shipment Fulfillment Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Fulfillment extends DataObject implements FulfillmentInterface
{
    /**
     * Get readable label for shipment method.
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->getData(FulfillmentInterface::SERVICE_NAME);
    }

    /**
     * Get Tracking Number for this shipment.
     *
     * @return string
     */
    public function getTrackingReference()
    {
        return $this->getData(FulfillmentInterface::TRACKING_REFERENCE);
    }

    /**
     * @return string
     */
    public function getCarrierName()
    {
        return $this->getData(FulfillmentInterface::CARRIER_NAME);
    }
}
