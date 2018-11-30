<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Filter;

/**
 * Webservice Filter
 *
 * Add filter parameters to web service list requests
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.temando.com/
 */
interface CollectionFilterInterface
{
    /**
     * @return string[]
     */
    public function getFilters();
}
