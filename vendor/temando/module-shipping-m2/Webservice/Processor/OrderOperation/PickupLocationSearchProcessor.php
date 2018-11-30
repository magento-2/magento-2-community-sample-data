<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchResultInterfaceFactory;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Model\Delivery\QuotePickupLocation;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Model\ResourceModel\Delivery\PickupLocationSearchResult;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Pickup Location Search Processor.
 *
 * Persist pickup location search result.
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupLocationSearchProcessor implements RatesProcessorInterface
{
    /**
     * @var PickupLocationSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * PickupLocationSearchProcessor constructor.
     * @param PickupLocationSearchResultInterfaceFactory $searchResultFactory
     */
    public function __construct(PickupLocationSearchResultInterfaceFactory $searchResultFactory)
    {
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Persist pickup locations from rates response.
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
        $searchRequest = $requestType->getPickupLocationSearchRequest();
        $pickupLocation = $requestType->getPickupLocation();

        if ($searchRequest === null) {
            // no search, no pickup location to persist
            return [];
        }

        if ($pickupLocation && $pickupLocation->getPickupLocationId()) {
            // delivery location was selected, no need to update pickup locations
            return [];
        }

        // persist pickup locations for a given search request
        $shippingAddressId = $searchRequest->getShippingAddressId();
        $pickupLocations = (array) $responseType->getPickupLocations();

        /** @var QuotePickupLocation $pickupLocation */
        foreach ($pickupLocations as $pickupLocation) {
            $pickupLocation->setData(QuotePickupLocationInterface::RECIPIENT_ADDRESS_ID, $shippingAddressId);
        }

        /** @var PickupLocationSearchResult $collection */
        $collection = $this->searchResultFactory->create();
        $collection->addFilter(QuotePickupLocationInterface::RECIPIENT_ADDRESS_ID, $shippingAddressId);
        $collection->walk('delete');

        $collection->setItems($pickupLocations);
        $collection->save();

        return [];
    }
}
