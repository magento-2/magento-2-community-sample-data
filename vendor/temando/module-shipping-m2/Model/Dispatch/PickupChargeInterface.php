<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Dispatch;

/**
 * Temando Dispatch Pickup Charge Interface.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface PickupChargeInterface
{
    const DESCRIPTION = 'description';
    const AMOUNT = 'amount';
    const CURRENCY = 'currency';

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @return string
     */
    public function getCurrency();
}
