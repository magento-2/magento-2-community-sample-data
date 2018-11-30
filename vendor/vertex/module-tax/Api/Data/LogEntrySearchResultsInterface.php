<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Api\Data;

/**
 * Data model representing a result from a search against the Vertex API Log
 *
 * @api
 */
interface LogEntrySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get log entry list.
     *
     * @return \Vertex\Tax\Api\Data\LogEntryInterface[]
     */
    public function getItems();

    /**
     * Set log entry list.
     *
     * @param \Vertex\Tax\Api\Data\LogEntryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
