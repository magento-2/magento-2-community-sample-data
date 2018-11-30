<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\CollectionPoint\CollectionPointSearchResultInterface;

/**
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface QuoteCollectionPointRepositoryInterface
{
    /**
     * Load collection point by entity id.
     *
     * @param int $entityId
     * @return QuoteCollectionPointInterface
     * @throws NoSuchEntityException
     */
    public function get($entityId);

    /**
     * Load selected collection point for given shipping address ID.
     *
     * @param int $addressId
     * @return QuoteCollectionPointInterface
     * @throws NoSuchEntityException
     */
    public function getSelected($addressId);

    /**
     * @param QuoteCollectionPointInterface $collectionPoint
     * @return QuoteCollectionPointInterface
     * @throws CouldNotSaveException
     */
    public function save(QuoteCollectionPointInterface $collectionPoint);

    /**
     * @param QuoteCollectionPointInterface $collectionPoint
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(QuoteCollectionPointInterface $collectionPoint);

    /**
     * Load collection points.
     *
     * @param SearchCriteriaInterface $criteria
     * @return CollectionPointSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $criteria);
}
