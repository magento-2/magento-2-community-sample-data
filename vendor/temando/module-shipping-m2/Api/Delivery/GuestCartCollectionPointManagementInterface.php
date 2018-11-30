<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Delivery;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Process Collection Point Search (Guest Checkout)
 *
 * @api
 * @package Temando\Shipping\Api
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface GuestCartCollectionPointManagementInterface
{
    /**
     * @param string $cartId
     * @param string $countryId
     * @param string $postcode
     * @return \Temando\Shipping\Api\Data\Delivery\CollectionPointSearchRequestInterface
     * @throws CouldNotSaveException
     */
    public function saveSearchRequest($cartId, $countryId, $postcode);

    /**
     * @param string $cartId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteSearchRequest($cartId);

    /**
     * @param string $cartId
     * @return \Temando\Shipping\Api\Data\Delivery\QuoteCollectionPointInterface[]
     */
    public function getCollectionPoints($cartId);

    /**
     * @param string $cartId
     * @param int $entityId
     * @return bool
     * @throws CouldNotSaveException
     */
    public function selectCollectionPoint($cartId, $entityId);
}
