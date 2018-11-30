<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Delivery;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\Delivery\CollectionPointSearchRequestInterface;
use Temando\Shipping\Api\Data\Delivery\CollectionPointSearchRequestInterfaceFactory;
use Temando\Shipping\Model\Delivery\CollectionPointSearchRequest;
use Temando\Shipping\Model\ResourceModel\Delivery\CollectionPointSearchRequest as SearchRequestResource;
use Temando\Shipping\Model\ResourceModel\Repository\CollectionPointSearchRepositoryInterface;

/**
 * Temando Collection Point Search Request Repository
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionPointSearchRequestRepository implements CollectionPointSearchRepositoryInterface
{
    /**
     * @var SearchRequestResource
     */
    private $resource;

    /**
     * @var CollectionPointSearchRequestInterfaceFactory
     */
    private $searchRequestFactory;

    /**
     * SearchRequestRepository constructor.
     * @param SearchRequestResource $resource
     * @param CollectionPointSearchRequestInterfaceFactory $searchRequestFactory
     */
    public function __construct(
        SearchRequestResource $resource,
        CollectionPointSearchRequestInterfaceFactory $searchRequestFactory
    ) {
        $this->resource = $resource;
        $this->searchRequestFactory = $searchRequestFactory;
    }

    /**
     * @param CollectionPointSearchRequestInterface $searchRequest
     * @return CollectionPointSearchRequestInterface
     * @throws CouldNotSaveException
     */
    public function save($searchRequest)
    {
        try {
            /** @var CollectionPointSearchRequest $searchRequest */
            $this->resource->save($searchRequest);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save search parameters.'), $exception);
        }

        return $searchRequest;
    }

    /**
     * @param int $quoteAddressId
     * @return CollectionPointSearchRequestInterface
     * @throws NoSuchEntityException
     */
    public function get($quoteAddressId)
    {
        /** @var CollectionPointSearchRequest $searchRequest */
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
        /** @var CollectionPointSearchRequest $searchRequest */
        $searchRequest = $this->searchRequestFactory->create(['data' => [
            CollectionPointSearchRequestInterface::SHIPPING_ADDRESS_ID => $quoteAddressId,
        ]]);

        try {
            $this->resource->delete($searchRequest);
        } catch (\Exception $exception) {
            $msg = __('Search request for address id "%1" could not be deleted.', $quoteAddressId);
            throw new CouldNotDeleteException($msg);
        }

        return $searchRequest->isDeleted();
    }
}
