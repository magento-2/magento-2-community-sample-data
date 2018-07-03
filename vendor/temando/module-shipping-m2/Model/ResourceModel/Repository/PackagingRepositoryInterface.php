<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Temando Packaging Repository Interface.
 *
 * Access packaging/container presets as defined for the merchant's account.
 * Presets can be used when creating a shipment instead of defining the
 * dimensions manually.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface PackagingRepositoryInterface
{
    /**
     * @param int|null $offset
     * @param int|null $limit
     * @return \Temando\Shipping\Model\PackagingInterface[]
     */
    public function getList($offset = null, $limit = null);

    /**
     * @param string $packagingId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete($packagingId);
}
