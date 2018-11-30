<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Api\Data\CollectionPoint\CollectionPointSearchResultInterface;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Repository\CollectionPointSearchRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\QuoteCollectionPointRepositoryInterface;

/**
 * Manage Collection Point Access
 *
 * @deprecated since 1.4.0
 * @see \Temando\Shipping\Model\Delivery\CollectionPointManagement
 *
 * @package Temando\Shipping\Model
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionPointManagement
{
    /**
     * @var CollectionPointSearchRepositoryInterface
     */
    private $searchRequestRepository;

    /**
     * @var SearchRequestInterfaceFactory
     */
    private $searchRequestFactory;

    /**
     * @var QuoteCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * CollectionPointManagement constructor.
     *
     * @param CollectionPointSearchRepositoryInterface $searchRequestRepository
     * @param SearchRequestInterfaceFactory $searchRequestFactory
     * @param QuoteCollectionPointRepositoryInterface $collectionPointRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CollectionPointSearchRepositoryInterface $searchRequestRepository,
        SearchRequestInterfaceFactory $searchRequestFactory,
        QuoteCollectionPointRepositoryInterface $collectionPointRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->searchRequestRepository = $searchRequestRepository;
        $this->searchRequestFactory = $searchRequestFactory;
        $this->collectionPointRepository = $collectionPointRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Save new search parameters, delete previous search results.
     *
     * @param int $addressId
     * @param string $countryId
     * @param string $postcode
     * @param bool $pending
     * @return \Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface
     * @throws CouldNotSaveException
     */
    public function saveSearchRequest($addressId, $countryId, $postcode, $pending = false)
    {
        $data = [
            SearchRequestInterface::SHIPPING_ADDRESS_ID => $addressId,
            SearchRequestInterface::PENDING => $pending,
        ];

        if ($countryId && $postcode) {
            $data[SearchRequestInterface::COUNTRY_ID] = $countryId;
            $data[SearchRequestInterface::POSTCODE] = $postcode;
        }

        $searchRequest = $this->searchRequestFactory->create(['data' => $data]);

        try {
            $this->searchRequestRepository->save($searchRequest);
            $this->deleteCollectionPoints($addressId);
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(__('Unable to save search parameters.'), $exception);
        }

        return $searchRequest;
    }

    /**
     * Delete search parameters, delete previous search results.
     *
     * @param int $addressId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteSearchRequest($addressId)
    {
        try {
            $this->searchRequestRepository->delete($addressId);
            $this->deleteCollectionPoints($addressId);
        } catch (LocalizedException $exception) {
            throw new CouldNotDeleteException(__('Unable to delete search parameters.'), $exception);
        }

        return true;
    }

    /**
     * Load all collection point search results for a given shipping address id.
     *
     * @param int $addressId
     * @return \Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface[]
     */
    public function getCollectionPoints($addressId)
    {
        $this->searchCriteriaBuilder->addFilter(
            QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID,
            $addressId
        );
        $criteria = $this->searchCriteriaBuilder->create();

        $searchResult = $this->collectionPointRepository->getList($criteria);

        return $searchResult->getItems();
    }

    /**
     * Delete all collection point search results for a given shipping address id.
     *
     * @param int $addressId
     * @return CollectionPointSearchResultInterface
     * @throws CouldNotDeleteException
     */
    public function deleteCollectionPoints($addressId)
    {
        $this->searchCriteriaBuilder->addFilter(
            QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID,
            $addressId
        );
        $criteria = $this->searchCriteriaBuilder->create();

        try {
            $searchResult = $this->collectionPointRepository->getList($criteria);
            $collectionPoints = $searchResult->getItems();
            array_walk($collectionPoints, function (QuoteCollectionPointInterface $collectionPoint) {
                $this->collectionPointRepository->delete($collectionPoint);
            });
        } catch (LocalizedException $exception) {
            throw new CouldNotDeleteException(__('Unable to delete collection points.'), $exception);
        }

        return $searchResult;
    }

    /**
     * Mark a collection point search result as selected for a given shipping address id.
     *
     * @param int $addressId
     * @param int $entityId
     * @return bool
     * @throws CouldNotSaveException
     */
    public function selectCollectionPoint($addressId, $entityId)
    {
        $collectionPoints = $this->getCollectionPoints($addressId);

        try {
            array_walk($collectionPoints, function (QuoteCollectionPointInterface $collectionPoint) use ($entityId) {
                $isSelected = ($entityId == $collectionPoint->getEntityId());
                /** @var $collectionPoint QuoteCollectionPoint */
                $collectionPoint->setData(QuoteCollectionPointInterface::SELECTED, $isSelected);
                $this->collectionPointRepository->save($collectionPoint);
            });
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to select collection point.'), $exception);
        }

        return true;
    }
}
