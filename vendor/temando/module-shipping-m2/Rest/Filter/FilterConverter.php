<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Filter;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Rest\EntityMapper\PointerAwareInterface;

/**
 * Temando REST API Filter Group Converter
 *
 * Convert Magento filter group to JSON API filter
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class FilterConverter
{
    private $conditionMap = [
        'from' => '',
        'to' => '',
        'eq' => 'equal',
        'neq' => 'notEqual',
        'like' => '',
        'in' => 'in',
        'nin' => 'notIn',
        'notnull' => '',
        'null' => '',
        'moreq' => '',
        'gt' => 'greaterThan',
        'lt' => 'lessThan',
        'gteq' => 'greaterThanOrEqual',
        'lteq' => 'lessThanOrEqual',
        'finset' => '',
        'regexp' => '',
        'seq' => '',
        'sneq' => '',
    ];

    /**
     * @var PointerAwareInterface
     */
    private $entityMapper;

    /**
     * @var PointerFilterFactory
     */
    private $filterFactory;

    /**
     * @var PointerFilterListFactory
     */
    private $filterListFactory;

    /**
     * Converter constructor.
     * @param PointerAwareInterface $entityMapper
     * @param PointerFilterFactory $filterFactory
     * @param PointerFilterListFactory $filterListFactory
     */
    public function __construct(
        PointerAwareInterface $entityMapper,
        PointerFilterFactory $filterFactory,
        PointerFilterListFactory $filterListFactory
    ) {
        $this->entityMapper = $entityMapper;
        $this->filterFactory = $filterFactory;
        $this->filterListFactory = $filterListFactory;
    }

    /**
     * Map a search criteria filter condition to an api condition.
     * @see $conditionMap
     *
     * @param string $conditionType
     * @return string
     * @throws LocalizedException
     */
    private function mapCondition($conditionType)
    {
        if (!isset($this->conditionMap[$conditionType])
            || (empty($this->conditionMap[$conditionType]))) {
            throw new LocalizedException(__('Filter condition %1 is not supported by the API.', $conditionType));
        }

        return $this->conditionMap[$conditionType];
    }

    /**
     * Convert a Magento filter into a platform JSON pointer filter.
     *
     * Filter conditions to expect from search criteria are documented as code comment:
     * @see \Magento\Framework\Data\Collection\AbstractDb::_getConditionSql
     * @link https://devdocs.magento.com/guides/v2.3/rest/performing-searches.html
     *
     * @param \Magento\Framework\Api\Filter $filter
     * @param \Closure|null $filterCallback
     * @return PointerFilter
     * @throws LocalizedException
     */
    public function convertFilter(Filter $filter, \Closure $filterCallback = null)
    {
        $path = $this->entityMapper->getPath($filter->getField());
        $operator = $this->mapCondition($filter->getConditionType());
        $value = $filterCallback ? $filterCallback($filter->getValue()) : $filter->getValue();

        $apiFilter = $this->filterFactory->create([
            'path' => $path,
            'operator' => $operator,
            'value' => $value,
        ]);

        return $apiFilter;
    }

    /**
     * Convert Magento filter groups to an API filter list.
     * API supports only conjunctive filters:
     * → multiple filter groups are supported (AND)
     * → multiple filters within one filter group are not supported (OR)
     * @link https://magento.stackexchange.com/a/91024
     *
     * @param FilterGroup[] $filterGroups
     * @param \Closure[] $filterCallbacks
     * @return PointerFilterList
     * @throws LocalizedException
     */
    public function convert(array $filterGroups, array $filterCallbacks)
    {
        $apiFilters = [];

        foreach ($filterGroups as $filterGroup) {
            $filters = $filterGroup->getFilters();
            if (empty($filters)) {
                continue;
            }

            // Logical disjunction is not supported by the API.
            $filter = current($filters);
            $filterCallback = isset($filterCallbacks[$filter->getField()])
                ? $filterCallbacks[$filter->getField()]
                : null;

            $apiFilters[]= $this->convertFilter($filter, $filterCallback);
        }

        $apiFilterList = $this->filterListFactory->create([
            'filters' => $apiFilters,
        ]);

        return $apiFilterList;
    }
}
