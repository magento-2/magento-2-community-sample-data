<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Delivery;

use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @api
 * @package Temando\Shipping\Api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface GuestCartPickupLocationManagementInterface
{
    /**
     * @param string $cartId
     * @return \Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface[]
     */
    public function getPickupLocations($cartId);

    /**
     * @param string $cartId
     * @param int $entityId
     * @return bool
     * @throws CouldNotSaveException
     */
    public function selectPickupLocation($cartId, $entityId);
}
