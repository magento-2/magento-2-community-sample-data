<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterfaceFactory;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Model\ResourceModel\Delivery\PickupLocationSearchResult;
use Temando\Shipping\Model\ResourceModel\Repository\QuotePickupLocationRepositoryInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Pickup Location Rates Processor.
 *
 * Read experiences/rates for a pickup location selected during checkout.
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupLocationRatesProcessor implements RatesProcessorInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var QuotePickupLocationRepositoryInterface
     */
    private $pickupLocationRepository;

    /**
     * @var ShippingExperienceInterfaceFactory
     */
    private $shippingExperienceFactory;

    /**
     * PickupLocationRatesProcessor constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QuotePickupLocationRepositoryInterface $pickupLocationRepository
     * @param ShippingExperienceInterfaceFactory $shippingExperienceFactory
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuotePickupLocationRepositoryInterface $pickupLocationRepository,
        ShippingExperienceInterfaceFactory $shippingExperienceFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->pickupLocationRepository = $pickupLocationRepository;
        $this->shippingExperienceFactory = $shippingExperienceFactory;
    }

    /**
     * Load shipping experiences for the selected pickup location.
     *
     * @param RateRequest $rateRequest
     * @param OrderInterface $requestType
     * @param OrderResponseTypeInterface $responseType
     * @return ShippingExperienceInterface[]
     */
    public function postProcess(
        RateRequest $rateRequest,
        OrderInterface $requestType,
        OrderResponseTypeInterface $responseType
    ) {
        $pickupLocation = $requestType->getPickupLocation();
        if ($pickupLocation === null) {
            // no selected pickup location in request
            return [];
        }

        $this->searchCriteriaBuilder->addFilter(
            QuotePickupLocationInterface::RECIPIENT_ADDRESS_ID,
            $pickupLocation->getRecipientAddressId()
        );
        $this->searchCriteriaBuilder->addFilter(
            QuotePickupLocationInterface::PICKUP_LOCATION_ID,
            $pickupLocation->getPickupLocationId()
        );
        $this->searchCriteriaBuilder->setPageSize(1);
        $this->searchCriteriaBuilder->setCurrentPage(1);

        $searchCriteria = $this->searchCriteriaBuilder->create();

        /** @var PickupLocationSearchResult $searchResult */
        $searchResult = $this->pickupLocationRepository->getList($searchCriteria);

        /** @var QuotePickupLocationInterface $pickupLocation */
        $pickupLocation = $searchResult->getFirstItem();

        $experiences = array_map(function (array $shippingExperience) {
            return $this->shippingExperienceFactory->create([
                ShippingExperienceInterface::CODE => $shippingExperience['code'],
                ShippingExperienceInterface::COST => $shippingExperience['cost'],
                ShippingExperienceInterface::LABEL => $shippingExperience['label'],
            ]);
        }, $pickupLocation->getShippingExperiences());

        return $experiences;
    }
}
