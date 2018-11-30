<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Api\Data\Delivery\CollectionPointSearchResultInterfaceFactory;
use Temando\Shipping\Api\Data\Delivery\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Model\Checkout\RateRequest\Extractor;
use Temando\Shipping\Model\Delivery\QuoteCollectionPoint;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Model\ResourceModel\Delivery\CollectionPointSearchResult;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Collection Point Search Processor.
 *
 * Persist collection points search result.
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionPointSearchProcessor implements RatesProcessorInterface
{
    /**
     * @var CollectionPointSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * CollectionPointSearchProcessor constructor.
     * @param CollectionPointSearchResultInterfaceFactory $searchResultFactory
     */
    public function __construct(CollectionPointSearchResultInterfaceFactory $searchResultFactory)
    {
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Persist collection points from rates response.
     *
     * @param RateRequest $rateRequest
     * @param OrderInterface $requestType
     * @param OrderResponseTypeInterface $responseType
     * @return ShippingExperienceInterface[]
     * @throws CouldNotSaveException
     */
    public function postProcess(
        RateRequest $rateRequest,
        OrderInterface $requestType,
        OrderResponseTypeInterface $responseType
    ) {
        $searchRequest = $requestType->getCollectionPointSearchRequest();
        $collectionPoint = $requestType->getCollectionPoint();

        if ($searchRequest === null) {
            // no search, no collection points to persist
            return [];
        }

        if ($searchRequest->isPending()) {
            // no search parameters submitted yet, no collection points to persist
            return [];
        }

        if ($collectionPoint && $collectionPoint->getCollectionPointId()) {
            // delivery location was selected, no need to update collection points
            return [];
        }

        // persist collection points for a given search request
        $shippingAddressId = $searchRequest->getShippingAddressId();
        $collectionPoints = (array) $responseType->getCollectionPoints();

        /** @var QuoteCollectionPoint $collectionPoint */
        foreach ($collectionPoints as $collectionPoint) {
            $collectionPoint->setData(QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID, $shippingAddressId);
        }

        /** @var CollectionPointSearchResult $collection */
        $collection = $this->searchResultFactory->create();
        $collection->addFilter(QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID, $shippingAddressId);
        $collection->walk('delete');

        $collection->setItems($collectionPoints);
        $collection->save();

        return [];
    }
}
