<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\Order;

/**
 * Temando Shipping Experience Interface
 *
 * Local representation of a shipping rate in checkout. A set of shipping
 * experiences is the result of creating an order at the Temando platform.
 *
 * (!) Needs to reside in Api namespace because selected shipping experience is
 * exposed to public as a ShippingInterface extension attribute.
 * @see \Magento\Sales\Api\Data\ShippingExtension::getShippingExperience()
 *
 * @api
 * @package  Temando\Shipping\Api
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ShippingExperienceInterface
{
    const LABEL = 'label';
    const CODE = 'code';
    const COST = 'cost';

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return float
     */
    public function getCost();
}
