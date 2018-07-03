<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

/**
 * Temando Dispatch Repository Interface.
 *
 * Access dispatch/completion documents.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface DispatchRepositoryInterface
{
    /**
     * @param string $dispatchId
     * @return \Temando\Shipping\Model\DispatchInterface
     */
    public function getById($dispatchId);

    /**
     * @param int|null $offset
     * @param int|null $limit
     * @return \Temando\Shipping\Model\DispatchInterface[]
     */
    public function getList($offset = null, $limit = null);
}
