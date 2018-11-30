<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\Delivery;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Pickup location search result interface
 *
 * @api
 * @package Temando\Shipping\Api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface PickupLocationSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface[]
     */
    public function getItems();

    /**
     * @param \Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
