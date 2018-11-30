<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Framework\DataObject;

/**
 * Temando Dispatch Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Dispatch extends DataObject implements DispatchInterface
{
    /**
     * @return string
     */
    public function getDispatchId()
    {
        return $this->getData(self::DISPATCH_ID);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @return string
     */
    public function getCarrierName()
    {
        return $this->getData(self::CARRIER_NAME);
    }

    /**
     * @return string[]
     */
    public function getCarrierMessages()
    {
        return $this->getData(self::CARRIER_MESSAGES);
    }

    /**
     * @return string
     */
    public function getCreatedAtDate()
    {
        return $this->getData(self::CREATED_AT_DATE);
    }

    /**
     * @return string
     */
    public function getReadyAtDate()
    {
        return $this->getData(self::READY_AT_DATE);
    }

    /**
     * @return string[]
     */
    public function getPickupNumbers()
    {
        return $this->getData(self::PICKUP_NUMBERS);
    }

    /**
     * @return \Temando\Shipping\Model\Dispatch\PickupChargeInterface[]
     */
    public function getPickupCharges()
    {
        return $this->getData(self::PICKUP_CHARGES);
    }

    /**
     * @return \Temando\Shipping\Model\Dispatch\ShipmentInterface[]
     */
    public function getIncludedShipments()
    {
        return $this->getData(self::INCLUDED_SHIPMENTS);
    }

    /**
     * @return \Temando\Shipping\Model\Dispatch\ShipmentInterface[]
     */
    public function getFailedShipments()
    {
        return $this->getData(self::FAILED_SHIPMENTS);
    }

    /**
     * @return DocumentationInterface[]
     */
    public function getDocumentation()
    {
        return $this->getData(self::DOCUMENTATION);
    }
}
