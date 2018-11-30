<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Api\Data\Delivery\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterfaceFactory;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Model\ResourceModel\Delivery\CollectionPointSearchResult;
use Temando\Shipping\Model\ResourceModel\Repository\QuoteCollectionPointRepositoryInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Collection Point Rates Processor.
 *
 * Read experiences/rates for a collection point selected during checkout.
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionPointRatesProcessor implements RatesProcessorInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var QuoteCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var ShippingExperienceInterfaceFactory
     */
    private $shippingExperienceFactory;

    /**
     * CollectionPointRatesProcessor constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QuoteCollectionPointRepositoryInterface $collectionPointRepository
     * @param ShippingExperienceInterfaceFactory $shippingExperienceFactory
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuoteCollectionPointRepositoryInterface $collectionPointRepository,
        ShippingExperienceInterfaceFactory $shippingExperienceFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionPointRepository = $collectionPointRepository;
        $this->shippingExperienceFactory = $shippingExperienceFactory;
    }

    /**
     * Load shipping experiences for the selected collection point.
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
        $collectionPoint = $requestType->getCollectionPoint();
        if ($collectionPoint === null) {
            // no selected collection point in request
            return [];
        }

        $this->searchCriteriaBuilder->addFilter(
            QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID,
            $collectionPoint->getRecipientAddressId()
        );
        $this->searchCriteriaBuilder->addFilter(
            QuoteCollectionPointInterface::COLLECTION_POINT_ID,
            $collectionPoint->getCollectionPointId()
        );
        $this->searchCriteriaBuilder->setPageSize(1);
        $this->searchCriteriaBuilder->setCurrentPage(1);

        $searchCriteria = $this->searchCriteriaBuilder->create();

        /** @var CollectionPointSearchResult $searchResult */
        $searchResult = $this->collectionPointRepository->getList($searchCriteria);

        /** @var QuoteCollectionPointInterface $collectionPoint */
        $collectionPoint = $searchResult->getFirstItem();

        $experiences = array_map(function (array $shippingExperience) {
            return $this->shippingExperienceFactory->create([
                ShippingExperienceInterface::CODE => $shippingExperience['code'],
                ShippingExperienceInterface::COST => $shippingExperience['cost'],
                ShippingExperienceInterface::LABEL => $shippingExperience['label'],
            ]);
        }, $collectionPoint->getShippingExperiences());

        return $experiences;
    }
}
