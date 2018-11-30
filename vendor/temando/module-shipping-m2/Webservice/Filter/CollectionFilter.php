<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Filter;

/**
 * API Request Filter List
 *
 * Example: /carriers?filter[registered]=true
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionFilter implements CollectionFilterInterface
{
    /**
     * @var string[]
     */
    private $filters;

    /**
     * CollectionFilter constructor.
     * @param string[] $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return string[]
     */
    public function getFilters()
    {
        return ['filter' => $this->filters];
    }
}
