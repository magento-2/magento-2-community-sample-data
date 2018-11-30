<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchRequestInterface;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchRequestInterfaceFactory;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchResultInterface;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Model\ResourceModel\Repository\PickupLocationSearchRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\QuotePickupLocationRepositoryInterface;

/**
 * Manage Pickup Location Access
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupLocationManagement
{
    /**
     * @var PickupLocationSearchRepositoryInterface
     */
    private $searchRequestRepository;

    /**
     * @var PickupLocationSearchRequestInterfaceFactory
     */
    private $searchRequestFactory;

    /**
     * @var QuotePickupLocationRepositoryInterface
     */
    private $pickupLocationRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * PickupLocationManagement constructor.
     *
     * @param PickupLocationSearchRepositoryInterface $searchRequestRepository
     * @param PickupLocationSearchRequestInterfaceFactory $searchRequestFactory
     * @param QuotePickupLocationRepositoryInterface $pickupLocationRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        PickupLocationSearchRepositoryInterface $searchRequestRepository,
        PickupLocationSearchRequestInterfaceFactory $searchRequestFactory,
        QuotePickupLocationRepositoryInterface $pickupLocationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->searchRequestRepository = $searchRequestRepository;
        $this->searchRequestFactory = $searchRequestFactory;
        $this->pickupLocationRepository = $pickupLocationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Load all collection location results for a given shipping address id.
     *
     * @param int $addressId
     * @return QuotePickupLocationInterface[]
     */
    public function getPickupLocations($addressId)
    {
        $this->searchCriteriaBuilder->addFilter(
            QuotePickupLocationInterface::RECIPIENT_ADDRESS_ID,
            $addressId
        );
        $criteria = $this->searchCriteriaBuilder->create();

        $searchResult = $this->pickupLocationRepository->getList($criteria);

        return $searchResult->getItems();
    }

    /**
     * Delete all collect location search results for a given shipping address id.
     *
     * @param int $addressId
     * @return PickupLocationSearchResultInterface
     * @throws CouldNotDeleteException
     */
    public function deletePickupLocations($addressId)
    {
        $this->searchCriteriaBuilder->addFilter(
            QuotePickupLocationInterface::RECIPIENT_ADDRESS_ID,
            $addressId
        );
        $criteria = $this->searchCriteriaBuilder->create();

        try {
            $searchResult = $this->pickupLocationRepository->getList($criteria);
            $pickupLocations = $searchResult->getItems();
            array_walk($pickupLocations, function (QuotePickupLocationInterface $pickupLocation) {
                $this->pickupLocationRepository->delete($pickupLocation);
            });
        } catch (LocalizedException $exception) {
            throw new CouldNotDeleteException(__('Unable to delete collect locations.'), $exception);
        }

        return $searchResult;
    }

    /**
     * Mark a pickup location search result as selected for a given shipping address id.
     *
     * @param int $addressId
     * @param int $entityId
     * @return bool
     * @throws CouldNotSaveException
     */
    public function selectPickupLocation($addressId, $entityId)
    {
        $pickupLocations = $this->getPickupLocations($addressId);

        try {
            array_walk($pickupLocations, function (QuotePickupLocationInterface $pickupLocation) use ($entityId) {
                $isSelected = ($entityId == $pickupLocation->getEntityId());
                /** @var QuotePickupLocation $pickupLocation*/
                $pickupLocation->setData(QuotePickupLocationInterface::SELECTED, $isSelected);
                $this->pickupLocationRepository->save($pickupLocation);
            });
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to select pickup location.'), $exception);
        }

        return true;
    }

    /**
     * Save new search parameters, delete previous search results.
     *
     * @param int $addressId
     * @param string $isActive
     * @return PickupLocationSearchRequestInterface
     * @throws CouldNotSaveException
     */
    public function saveSearchRequest($addressId, $isActive)
    {
        $searchRequest = $this->searchRequestFactory->create(['data' => [
            PickupLocationSearchRequestInterface::SHIPPING_ADDRESS_ID => $addressId,
            PickupLocationSearchRequestInterface::ACTIVE => $isActive,
        ]]);

        try {
            $this->searchRequestRepository->save($searchRequest);
            $this->deletePickupLocations($addressId);
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
            $this->deletePickupLocations($addressId);
        } catch (LocalizedException $exception) {
            throw new CouldNotDeleteException(__('Unable to delete search parameters.'), $exception);
        }

        return true;
    }
}
