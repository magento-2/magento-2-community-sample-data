<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Api;

/**
 * Service Contract for retrieving, saving, and removing Vertex log entries
 *
 * @api
 */
interface LogEntryRepositoryInterface
{
    /**
     * Save a Vertex Log Entry
     *
     * @param \Vertex\Tax\Api\Data\LogEntryInterface $logEntry
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Vertex\Tax\Api\Data\LogEntryInterface $logEntry);

    /**
     * Retrieve a collection of Vertex Log Entries based on the provided Search Criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vertex\Tax\Api\Data\LogEntrySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete a Vertex Log Entry
     *
     * @param \Vertex\Tax\Api\Data\LogEntryInterface $logEntry
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Vertex\Tax\Api\Data\LogEntryInterface $logEntry);

    /**
     * Delete a Vertex Log Entry
     *
     * @param int $logEntryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($logEntryId);
}
