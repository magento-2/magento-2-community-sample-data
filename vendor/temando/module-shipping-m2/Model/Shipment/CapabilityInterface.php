<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Capability Interface.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CapabilityInterface
{
    const CAPABILITY_ID = 'capability_id';
    const PROPERTIES = 'properties';

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getCapabilityId();

    /**
     * Get attribute value
     *
     * @return mixed[]
     */
    public function getProperties();
}
