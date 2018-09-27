<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Quote;

/**
 * Interface GuestCartCheckoutFieldManagementInterface
 *
 * Persist value-added services as selected during guest checkout.
 *
 * @api
 * @package Temando\Shipping\Api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
interface GuestCartCheckoutFieldManagementInterface
{
    /**
     * @param string $cartId
     * @param \Magento\Framework\Api\AttributeInterface[] $serviceSelection
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveCheckoutFields($cartId, $serviceSelection);
}
