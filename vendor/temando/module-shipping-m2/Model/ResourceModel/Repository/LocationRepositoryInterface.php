<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Temando Location Repository Interface.
 *
 * Access the origin locations as defined for the merchant's account.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface LocationRepositoryInterface
{
    /**
     * @param int|null $offset
     * @param int|null $limit
     * @return \Temando\Shipping\Model\LocationInterface[]
     */
    public function getList($offset = null, $limit = null);

    /**
     * @param string $locationId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete($locationId);
}
