<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Pagination;

/**
 * Webservice Pagination
 *
 * Request param style, example: /locations?limit=200
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Pagination implements PaginationInterface
{
    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * Pagination constructor.
     * @param int $offset
     * @param int $limit
     */
    public function __construct($offset = null, $limit = null)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * @return string[]
     */
    public function getPageParams()
    {
        if ($this->limit === null) {
            return [];
        }

        $params = [
            'offset' => (int)$this->offset,
            'limit'  => (int)$this->limit,
        ];

        return $params;
    }
}
