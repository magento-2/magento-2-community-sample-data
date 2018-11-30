<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Webservice;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;

/**
 * Temando API Resource Collection
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
abstract class Collection extends DataCollection implements SearchResultInterface
{
    /**
     * @var string
     */
    protected $_itemObjectClass = Document::class;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Filter[]
     */
    private $filters = [];

    /**
     * @var int
     */
    private $totalCount;

    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param ManagerInterface $messageManager
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ManagerInterface $messageManager,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->messageManager = $messageManager;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

        parent::__construct($entityFactory);
    }

    /**
     * @param string|array $field
     * @param string|int|array $condition
     * @throws \Magento\Framework\Exception\LocalizedException if some error in the input could be detected.
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFieldToFilter($field, $condition)
    {
        foreach ($condition as $type => $value) {
            $this->filters[]= $this->filterBuilder
                ->setField($field)
                ->setValue($value)
                ->setConditionType($type)
                ->create();
        }

        return $this;
    }

    /**
     * Perform API call
     *
     * @param SearchCriteriaInterface $criteria
     * @return \Magento\Framework\DataObject[]
     */
    abstract public function fetchData(SearchCriteriaInterface $criteria);

    /**
     * Sort documents
     * @return void
     */
    private function sortItems()
    {
        foreach ($this->_orders as $field => $direction) {
            if ($direction === self::SORT_ORDER_ASC) {
                uasort($this->_items, function (Document $itemA, Document $itemB) use ($field, $direction) {
                    return $itemA->getDataByKey($field) > $itemB->getDataByKey($field) ? 1 : -1;
                });
            } else {
                uasort($this->_items, function (Document $itemA, Document $itemB) use ($field, $direction) {
                    return $itemA->getDataByKey($field) < $itemB->getDataByKey($field) ? 1 : -1;
                });
            }
        }
    }

    /**
     * Paginate documents
     * @return void
     */
    private function sliceItems()
    {
        $offset = $this->_pageSize * ($this->_curPage -1);
        $limit = $this->_pageSize;

        $this->_items = array_slice($this->_items, $offset, $limit);
    }

    /**
     * Load data from repository/api and convert to Document class
     *
     * @see \Magento\Framework\Data\Collection\AbstractDb::loadWithFilter()
     * @see \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider::searchResultToOutput()
     * @see \Magento\Framework\Api\Search\SearchResultInterface::getItems()
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        try {
            // load list from webservice
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilters($this->filters)
                ->create();
            $data = $this->fetchData($searchCriteria);

            // shift response items to document class
            foreach ($data as $apiItem) {
                $item = $this->getNewEmptyItem();
                $item->addData($apiItem->getData());
                $this->addItem($item);
            }

            $this->_totalRecords = count($this->_items);
            $this->sortItems();
            $this->sliceItems();
        } catch (LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e, 'An error occurred while requesting API listing.');
        }

        $this->_setIsLoaded();
        return $this;
    }

    /**
     * Set items list.
     *
     * @param DocumentInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        if ($items) {
            foreach ($items as $item) {
                $this->addItem($item);
            }
            unset($this->totalCount);
        }
        return $this;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }

    /**
     * Set search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        $this->searchCriteria = $searchCriteria;
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        if (!$this->totalCount) {
            $this->totalCount = $this->getSize();
        }
        return $this->totalCount;
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
        return $this;
    }
}
