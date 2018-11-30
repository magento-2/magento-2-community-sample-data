<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Pagination;

use Temando\Shipping\Webservice\Pagination\PaginationInterface;

/**
 * Webservice Pagination
 *
 * JSON API style, example: /completions?page[limit]=200&page[offset]=400
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
            'page' => [
                'offset' => (int)$this->offset,
                'limit'  => (int)$this->limit,
            ],
        ];

        return $params;
    }
}
