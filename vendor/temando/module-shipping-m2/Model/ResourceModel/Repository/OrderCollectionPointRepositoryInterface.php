<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\CollectionPoint\OrderCollectionPointInterface;

/**
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface OrderCollectionPointRepositoryInterface
{
    /**
     * Load collection point by shipping address id.
     *
     * @param int $addressId
     * @return OrderCollectionPointInterface
     * @throws NoSuchEntityException
     */
    public function get($addressId);

    /**
     * @param OrderCollectionPointInterface $collectionPoint
     * @return OrderCollectionPointInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderCollectionPointInterface $collectionPoint);
}
