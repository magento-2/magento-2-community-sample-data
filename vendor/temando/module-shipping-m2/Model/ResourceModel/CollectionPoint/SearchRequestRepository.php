<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\CollectionPoint;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterfaceFactory;
use Temando\Shipping\Model\CollectionPoint\SearchRequest;
use Temando\Shipping\Model\ResourceModel\CollectionPoint\SearchRequest as SearchRequestResource;
use Temando\Shipping\Model\ResourceModel\Repository\CollectionPointSearchRepositoryInterface;

/**
 * Temando Collection Point Search Request Repository
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class SearchRequestRepository implements CollectionPointSearchRepositoryInterface
{
    /**
     * @var SearchRequestResource
     */
    private $resource;

    /**
     * @var SearchRequestInterfaceFactory
     */
    private $searchRequestFactory;

    /**
     * SearchRequestRepository constructor.
     * @param SearchRequestResource $resource
     * @param SearchRequestInterfaceFactory $searchRequestFactory
     */
    public function __construct(
        SearchRequestResource $resource,
        SearchRequestInterfaceFactory $searchRequestFactory
    ) {
        $this->resource = $resource;
        $this->searchRequestFactory = $searchRequestFactory;
    }

    /**
     * @param SearchRequestInterface $searchRequest
     * @return SearchRequestInterface
     * @throws CouldNotSaveException
     */
    public function save($searchRequest)
    {
        try {
            /** @var SearchRequest $searchRequest */
            $this->resource->save($searchRequest);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save search parameters.'), $exception);
        }

        return $searchRequest;
    }

    /**
     * @param int $quoteAddressId
     * @return SearchRequestInterface
     * @throws NoSuchEntityException
     */
    public function get($quoteAddressId)
    {
        /** @var SearchRequest $searchRequest */
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
        /** @var \Temando\Shipping\Model\CollectionPoint\SearchRequest $searchRequest */
        $searchRequest = $this->searchRequestFactory->create(['data' => [
            SearchRequestInterface::SHIPPING_ADDRESS_ID => $quoteAddressId,
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
