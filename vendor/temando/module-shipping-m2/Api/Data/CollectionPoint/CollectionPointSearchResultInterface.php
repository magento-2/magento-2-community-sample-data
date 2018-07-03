<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\CollectionPoint;

use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Collection point search result interface
 *
 * @api
 * @package  Temando\Shipping\Api
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CollectionPointSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return \Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
