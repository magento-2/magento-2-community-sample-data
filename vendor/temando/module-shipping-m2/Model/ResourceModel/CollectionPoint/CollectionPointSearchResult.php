<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\CollectionPoint;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Temando\Shipping\Api\Data\CollectionPoint\CollectionPointSearchResultInterface;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Model\CollectionPoint\QuoteCollectionPoint;
use Temando\Shipping\Model\ResourceModel\CollectionPoint\QuoteCollectionPoint as CollectionPointResource;

/**
 * Collection point collection
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CollectionPointSearchResult extends AbstractCollection implements CollectionPointSearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'temando_collection_point_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'collection_point_collection';

    /**
     * Init collection and determine table names
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(QuoteCollectionPoint::class, CollectionPointResource::class);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
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
        return $this->getSize();
    }

    /**
     * Not applicable, Collection vs. Search Result seems to be work in progress.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param QuoteCollectionPointInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        if (!$items) {
            return $this;
        }
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }

    /**
     * Unserialize opening_hours in each item
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        /** @var QuoteCollectionPointInterface $item */
        foreach ($this->_items as $item) {
            if (is_string($item->getOpeningHours())) {
                $this->getResource()->unserializeFields($item);
            }
        }

        return parent::_afterLoad();
    }
}
