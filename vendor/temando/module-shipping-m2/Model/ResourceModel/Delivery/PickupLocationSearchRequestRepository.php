<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Delivery;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchRequestInterface;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchRequestInterfaceFactory;
use Temando\Shipping\Model\Delivery\PickupLocationSearchRequest;
use Temando\Shipping\Model\ResourceModel\Delivery\PickupLocationSearchRequest as SearchRequestResource;
use Temando\Shipping\Model\ResourceModel\Repository\PickupLocationSearchRepositoryInterface;

/**
 * Temando Pickup Location Search Request Repository
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupLocationSearchRequestRepository implements PickupLocationSearchRepositoryInterface
{
    /**
     * @var SearchRequestResource
     */
    private $resource;

    /**
     * @var PickupLocationSearchRequestInterfaceFactory
     */
    private $searchRequestFactory;

    /**
     * SearchRequestRepository constructor.
     * @param SearchRequestResource $resource
     * @param PickupLocationSearchRequestInterfaceFactory $searchRequestFactory
     */
    public function __construct(
        SearchRequestResource $resource,
        PickupLocationSearchRequestInterfaceFactory $searchRequestFactory
    ) {
        $this->resource = $resource;
        $this->searchRequestFactory = $searchRequestFactory;
    }

    /**
     * @param PickupLocationSearchRequestInterface $searchRequest
     * @return PickupLocationSearchRequestInterface
     * @throws CouldNotSaveException
     */
    public function save($searchRequest)
    {
        try {
            /** @var PickupLocationSearchRequest $searchRequest */
            $this->resource->save($searchRequest);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save search parameters.'), $exception);
        }

        return $searchRequest;
    }

    /**
     * @param int $quoteAddressId
     * @return PickupLocationSearchRequestInterface
     * @throws NoSuchEntityException
     */
    public function get($quoteAddressId)
    {
        /** @var PickupLocationSearchRequest $searchRequest */
        $searchRequest = $this->searchRequestFactory->create();
        $this->resource->load($searchRequest, $quoteAddressId);

        if (!$searchRequest->getShippingAddressId()) {
            throw new NoSuchEntityException(__('Search request for address id "%1" does not exist.', $quoteAddressId));
        }

        return $searchRequest;
    }

    /**
     * @param int $quoteAddressId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete($quoteAddressId)
    {
        $searchRequest = $this->searchRequestFactory->create(['data' => [
            PickupLocationSearchRequestInterface::SHIPPING_ADDRESS_ID => $quoteAddressId,
        ]]);

        try {
            /** @var $searchRequest PickupLocationSearchRequest */
            $this->resource->delete($searchRequest);
        } catch (\Exception $exception) {
            $msg = __('Search request for address id "%1" could not be deleted.', $quoteAddressId);
            throw new CouldNotDeleteException($msg);
        }

        return $searchRequest->isDeleted();
    }
}
