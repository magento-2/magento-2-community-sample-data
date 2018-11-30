<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Dispatch;

use Magento\Framework\DataObject;

/**
 * Temando Dispatch Pickup Charge
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupCharge extends DataObject implements PickupChargeInterface
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(PickupChargeInterface::DESCRIPTION);
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->getData(PickupChargeInterface::AMOUNT);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getData(PickupChargeInterface::CURRENCY);
    }
}
