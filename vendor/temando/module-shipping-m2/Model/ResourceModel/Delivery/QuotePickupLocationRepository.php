<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Delivery;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchResultInterface;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchResultInterfaceFactory;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterfaceFactory;
use Temando\Shipping\Model\Delivery\QuotePickupLocation;
use Temando\Shipping\Model\ResourceModel\Delivery\QuotePickupLocation as PickupLocationResource;
use Temando\Shipping\Model\ResourceModel\Repository\QuotePickupLocationRepositoryInterface;

/**
 * Temando Quote Pickup Location Repository
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class QuotePickupLocationRepository implements QuotePickupLocationRepositoryInterface
{
    /**
     * @var PickupLocationResource
     */
    private $resource;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var QuotePickupLocationInterfaceFactory
     */
    private $pickupLocationFactory;

    /**
     * @var PickupLocationSearchResultInterfaceFactory
     */
    private $pickupLocationSearchResultFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * PickupLocationRepository constructor.
     * @param PickupLocationResource $resource
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QuotePickupLocationInterfaceFactory $pickupLocationFactory
     * @param PickupLocationSearchResultInterfaceFactory $pickupLocationSearchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        PickupLocationResource $resource,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuotePickupLocationInterfaceFactory $pickupLocationFactory,
        PickupLocationSearchResultInterfaceFactory $pickupLocationSearchResultFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->pickupLocationFactory = $pickupLocationFactory;
        $this->pickupLocationSearchResultFactory = $pickupLocationSearchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Load pickup location by entity id.
     *
     * @param int $entityId
     * @return QuotePickupLocationInterface
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        /** @var QuotePickupLocation $pickupLocation */
        $pickupLocation = $this->pickupLocationFactory->create();

        try {
            $this->resource->load($pickupLocation, $entityId);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Pickup location with id "%1" does not exist.', $entityId));
        }

        return $pickupLocation;
    }

    /**
     * Load selected pickup location for given shipping address ID.
     *
     * Beware: AbstractDb::fetchItem will NOT decode serialized fields.
     *
     * @param int $addressId
     * @return QuotePickupLocationInterface
     * @throws NoSuchEntityException
     */
    public function getSelected($addressId)
    {
        $this->searchCriteriaBuilder->addFilter(
            QuotePickupLocationInterface::RECIPIENT_ADDRESS_ID,
            $addressId
        );
        $this->searchCriteriaBuilder->addFilter(
            QuotePickupLocationInterface::SELECTED,
            true
        );
        $this->searchCriteriaBuilder->setPageSize(1);
        $this->searchCriteriaBuilder->setCurrentPage(1);

        $criteria = $this->searchCriteriaBuilder->create();

        /** @var PickupLocationSearchResult $searchResult */
        $searchResult = $this->getList($criteria);

        /** @var QuotePickupLocationInterface $pickupLocation */
        $pickupLocation = $searchResult->fetchItem();
        if (!$pickupLocation) {
            $msg = __('Selected pickup location for address id "%1" does not exist.', $addressId);
            throw new NoSuchEntityException($msg);
        }

        return $pickupLocation;
    }

    /**
     * @param QuotePickupLocationInterface $pickupLocation
     * @return QuotePickupLocationInterface
     * @throws CouldNotSaveException
     */
    public function save(QuotePickupLocationInterface $pickupLocation)
    {
        try {
            /** @var QuotePickupLocation $pickupLocation */
            $this->resource->save($pickupLocation);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save pickup location.'), $exception);
        }

        return $pickupLocation;
    }

    /**
     * @param QuotePickupLocationInterface $pickupLocation
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(QuotePickupLocationInterface $pickupLocation)
    {
        try {
            /** @var QuotePickupLocation $pickupLocation */
            $this->resource->delete($pickupLocation);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Unable to delete pickup location.'), $exception);
        }

        return true;
    }

    /**
     * Load pickup locations.
     *
     * @param SearchCriteriaInterface $criteria
     * @return PickupLocationSearchResultInterface|PickupLocationSearchResult
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var PickupLocationSearchResult $searchResult */
        $searchResult = $this->pickupLocationSearchResultFactory->create();

        $this->collectionProcessor->process($criteria, $searchResult);
        $searchResult->setSearchCriteria($criteria);

        return $searchResult;
    }
}
