<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request;

/**
 * Temando API Item Listing Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ListRequest implements ListRequestInterface
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
     * @var string[]
     */
    private $filter;

    /**
     * GetList constructor.
     * @param int $offset
     * @param int $limit
     * @param string[] $filter
     */
    public function __construct($offset, $limit, array $filter = [])
    {
        $this->limit  = $limit;
        $this->offset = $offset;
        $this->filter = $filter;
    }

    /**
     * Retrieve query parameters for listings.
     *
     * @return string[]
     */
    public function getRequestParams()
    {
        $requestParams = [
            'offset' => $this->offset,
            'limit'  => $this->limit
        ];

        if (!empty($this->filter)) {
            $requestParams['filter'] = $this->filter;
        }

        return $requestParams;
    }
}
