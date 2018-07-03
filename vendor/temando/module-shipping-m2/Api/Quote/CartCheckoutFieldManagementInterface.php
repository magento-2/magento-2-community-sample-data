<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Quote;

/**
 * Interface CartCheckoutFieldManagementInterface
 *
 * Persist value-added services as selected during logged-in checkout.
 *
 * @api
 * @package Temando\Shipping\Api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
interface CartCheckoutFieldManagementInterface
{
    /**
     * @param int $cartId
     * @param \Magento\Framework\Api\AttributeInterface[] $serviceSelection
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveCheckoutFields($cartId, $serviceSelection);
}
