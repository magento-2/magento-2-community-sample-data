<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Filter;

use Magento\Framework\Serialize\Serializer\Json;
use Temando\Shipping\Webservice\Filter\CollectionFilterInterface;

/**
 * JSON API Pointer Filter List
 *
 * Example: /fulfillments?filter=[{"path":"/pickUpLocation","operator":"equal","value":"123-456"}]
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PointerFilterList implements CollectionFilterInterface
{
    /**
     * @var PointerFilter[]
     */
    private $filters;

    /**
     * PointerFilterList constructor.
     * @param PointerFilter[] $filters
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
        return ['filter' => json_encode($this->filters, JSON_UNESCAPED_SLASHES)];
    }
}
