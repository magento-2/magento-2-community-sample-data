<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\CollectionPoint;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\CollectionPoint\CollectionPointSearchResultInterface;
use Temando\Shipping\Api\Data\CollectionPoint\CollectionPointSearchResultInterfaceFactory;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterfaceFactory;
use Temando\Shipping\Model\CollectionPoint\QuoteCollectionPoint;
use Temando\Shipping\Model\ResourceModel\CollectionPoint\QuoteCollectionPoint as CollectionPointResource;
use Temando\Shipping\Model\ResourceModel\Repository\QuoteCollectionPointRepositoryInterface;

/**
 * Temando Quote Collection Point Repository
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class QuoteCollectionPointRepository implements QuoteCollectionPointRepositoryInterface
{
    /**
     * @var CollectionPointResource
     */
    private $resource;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var QuoteCollectionPointInterfaceFactory
     */
    private $collectionPointFactory;

    /**
     * @var CollectionPointSearchResultInterfaceFactory
     */
    private $collectionPointSearchResultFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * CollectionPointRepository constructor.
     * @param CollectionPointResource $resource
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QuoteCollectionPointInterfaceFactory $collectionPointFactory
     * @param CollectionPointSearchResultInterfaceFactory $collectionPointSearchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        CollectionPointResource $resource,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuoteCollectionPointInterfaceFactory $collectionPointFactory,
        CollectionPointSearchResultInterfaceFactory $collectionPointSearchResultFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionPointFactory = $collectionPointFactory;
        $this->collectionPointSearchResultFactory = $collectionPointSearchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Load collection point by entity id.
     *
     * @param int $entityId
     * @return QuoteCollectionPointInterface
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        /** @var QuoteCollectionPoint $collectionPoint */
        $collectionPoint = $this->collectionPointFactory->create();

        try {
            $this->resource->load($collectionPoint, $entityId);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Collection point with id "%1" does not exist.', $entityId));
        }

        return $collectionPoint;
    }

    /**
     * Load selected collection point for given shipping address ID.
     *
     * Beware: AbstractDb::fetchItem will NOT decode serialized fields.
     *
     * @param int $addressId
     * @return QuoteCollectionPointInterface
     * @throws NoSuchEntityException
     */
    public function getSelected($addressId)
    {
        $this->searchCriteriaBuilder->addFilter(
            QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID,
            $addressId
        );
        $this->searchCriteriaBuilder->addFilter(
            QuoteCollectionPointInterface::SELECTED,
            true
        );
        $this->searchCriteriaBuilder->setPageSize(1);
        $this->searchCriteriaBuilder->setCurrentPage(1);

        $criteria = $this->searchCriteriaBuilder->create();

        /** @var CollectionPointSearchResult $searchResult */
        $searchResult = $this->getList($criteria);

        /** @var QuoteCollectionPointInterface $collectionPoint */
        $collectionPoint = $searchResult->fetchItem();
        if (!$collectionPoint) {
            $msg = __('Selected collection point for address id "%1" does not exist.', $addressId);
            throw new NoSuchEntityException($msg);
        }

        return $collectionPoint;
    }

    /**
     * @param QuoteCollectionPointInterface $collectionPoint
     * @return QuoteCollectionPointInterface
     * @throws CouldNotSaveException
     */
    public function save(QuoteCollectionPointInterface $collectionPoint)
    {
        try {
            /** @var QuoteCollectionPoint $collectionPoint */
            $this->resource->save($collectionPoint);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save collection point.'), $exception);
        }

        return $collectionPoint;
    }

    /**
     * @param QuoteCollectionPointInterface $collectionPoint
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(QuoteCollectionPointInterface $collectionPoint)
    {
        try {
            $this->resource->delete($collectionPoint);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Unable to delete collection point.'), $exception);
        }

        return true;
    }

    /**
     * Load collection points.
     *
     * @param SearchCriteriaInterface $criteria
     * @return CollectionPointSearchResultInterface|CollectionPointSearchResult
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var CollectionPointSearchResult $searchResult */
        $searchResult = $this->collectionPointSearchResultFactory->create();

        $this->collectionProcessor->process($criteria, $searchResult);
        $searchResult->setSearchCriteria($criteria);

        return $searchResult;
    }
}
