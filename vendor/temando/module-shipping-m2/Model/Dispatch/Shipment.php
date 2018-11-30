<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Dispatch;

use Magento\Framework\DataObject;

/**
 * Temando Dispatch Shipment
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Shipment extends DataObject implements ShipmentInterface
{
    /**
     * @return string
     */
    public function getShipmentId()
    {
        return $this->getData(ShipmentInterface::SHIPMENT_ID);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(ShipmentInterface::STATUS);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getData(ShipmentInterface::MESSAGE);
    }

    /**
     * @return ErrorInterface
     */
    public function getErrors()
    {
        return $this->getData(ShipmentInterface::ERRORS);
    }
}
