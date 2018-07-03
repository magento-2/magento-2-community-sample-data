<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use \Magento\Framework\DataObject;

/**
 * Temando Shipment Capability Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Capability extends DataObject implements CapabilityInterface
{
    /**
     * Get attribute code
     *
     * @return string
     */
    public function getCapabilityId()
    {
        return $this->getData(CapabilityInterface::CAPABILITY_ID);
    }

    /**
     * Get attribute value
     *
     * @return mixed[]
     */
    public function getProperties()
    {
        return $this->getData(CapabilityInterface::PROPERTIES);
    }
}
