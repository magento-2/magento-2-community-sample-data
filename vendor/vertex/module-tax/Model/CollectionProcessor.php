<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\ObjectManagerInterface;

/**
 * Provides backwards compatibility for Magento's Collection Processor, to ease implementation of our Repositorys'
 * getList methods
 */
class CollectionProcessor
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Perform the requirements of a Search Criteria against a collection
     *
     * Use instead {@see CollectionProcessorInterface::process()} if available
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AbstractDb $collection
     * @return self
     */
    public function process(SearchCriteriaInterface $searchCriteria, AbstractDb $collection)
    {
        if (\class_exists(CollectionProcessorInterface::class)) {
            // We support a lower version than the introduction of CollectionProcessorInterface
            // Object Manager necessary for feature detection
            $collectionProcessor = $this->objectManager->get(CollectionProcessorInterface::class);
            return $collectionProcessor->process($searchCriteria, $collection);
        }

        $this->processFilters($searchCriteria, $collection);
        $this->processSort($searchCriteria, $collection);

        return $this;
    }

    /**
     * Perform SearchCriteria's filters against the collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AbstractDb $collection
     */
    private function processFilters(SearchCriteriaInterface $searchCriteria, AbstractDb $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
    }

    /**
     * Add a Filter Group to a collection
     *
     * @param FilterGroup $group
     * @param AbstractDb $collection
     */
    private function addFilterGroupToCollection(FilterGroup $group, AbstractDb $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($group->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Perform the SearchCriteria's sort against the collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AbstractDb $collection
     */
    private function processSort(SearchCriteriaInterface $searchCriteria, AbstractDb $collection)
    {
        if (empty($searchCriteria->getSortOrders())) {
            return;
        }

        foreach ($searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $order = $sortOrder->getDirection() === SortOrder::SORT_ASC
                ? Collection::SORT_ORDER_ASC
                : Collection::SORT_ORDER_DESC;

            $collection->addOrder($field, $order);
        }
    }
}
