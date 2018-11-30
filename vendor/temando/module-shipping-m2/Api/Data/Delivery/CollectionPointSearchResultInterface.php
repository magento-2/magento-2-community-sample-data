<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\Delivery;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Collection point search result interface
 *
 * @api
 * @package Temando\Shipping\Api
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface CollectionPointSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Temando\Shipping\Api\Data\Delivery\QuoteCollectionPointInterface[]
     */
    public function getItems();

    /**
     * @param \Temando\Shipping\Api\Data\Delivery\QuoteCollectionPointInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
