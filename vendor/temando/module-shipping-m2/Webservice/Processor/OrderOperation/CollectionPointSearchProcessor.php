<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Api\Data\CollectionPoint\CollectionPointSearchResultInterfaceFactory;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Model\CollectionPoint\QuoteCollectionPoint;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Model\ResourceModel\CollectionPoint\CollectionPointSearchResult;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Collection Point Search Processor.
 *
 * Persist collection points search result.
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class CollectionPointSearchProcessor implements RatesProcessorInterface
{
    /**
     * @var CollectionPointSearchResultInterfaceFactory
     */
    private $collectionPointSearchResultFactory;

    /**
     * CollectionPointSearchProcessor constructor.
     * @param CollectionPointSearchResultInterfaceFactory $collectionPointSearchResultFactory
     */
    public function __construct(CollectionPointSearchResultInterfaceFactory $collectionPointSearchResultFactory)
    {
        $this->collectionPointSearchResultFactory = $collectionPointSearchResultFactory;
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
        $shippingAddressId = $searchRequest->getShippingAddressId();
        $isPending = $searchRequest->isPending();

        // persist collection points for a given search request
        if ($shippingAddressId && !$isPending) {
            $collectionPoints = (array) $responseType->getCollectionPoints();

            /** @var QuoteCollectionPoint $collectionPoint */
            foreach ($collectionPoints as $collectionPoint) {
                $collectionPoint->setData(QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID, $shippingAddressId);
            }

            /** @var CollectionPointSearchResult $collection */
            $collection = $this->collectionPointSearchResultFactory->create();
            $collection->addFilter(QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID, $shippingAddressId);
            $collection->setItems($collectionPoints);
            $collection->save();
        }

        return [];
    }
}
