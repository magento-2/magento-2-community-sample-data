<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\CollectionPoint;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection;
use Temando\Shipping\Api\Data\CollectionPoint\CollectionPointSearchResultInterface;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;

/**
 * Collection point collection
 *
 * @deprecated since 1.4.0
 * @see \Temando\Shipping\Model\ResourceModel\Delivery\CollectionPointSearchResult
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionPointSearchResult extends Collection implements CollectionPointSearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;

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
}
