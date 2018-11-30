<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\DataObject;

/**
 * Temando Shipment Summary
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class ShipmentSummary extends DataObject implements ShipmentSummaryInterface
{
    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->getData(ShipmentSummaryInterface::ORDER_ID);
    }

    /**
     * @return string
     */
    public function getShipmentId()
    {
        return $this->getData(ShipmentSummaryInterface::SHIPMENT_ID);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(ShipmentSummaryInterface::STATUS);
    }

    /**
     * @return string
     */
    public function getRecipientAddress()
    {
        return $this->getData(ShipmentSummaryInterface::RECIPIENT_ADDRESS);
    }

    /**
     * @return string
     */
    public function getRecipientName()
    {
        return $this->getData(ShipmentSummaryInterface::RECIPIENT_NAME);
    }

    /**
     * @return ShipmentErrorInterface[]
     */
    public function getErrors()
    {
        return $this->getData(ShipmentSummaryInterface::ERRORS);
    }
}
