<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterfaceFactory;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Model\ResourceModel\CollectionPoint\CollectionPointSearchResult;
use Temando\Shipping\Model\ResourceModel\Repository\QuoteCollectionPointRepositoryInterface;
use Temando\Shipping\Model\Shipping\RateRequest\Extractor;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Collection Point Rates Processor.
 *
 * Read experiences/rates for a collection point selected during checkout.
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class CollectionPointRatesProcessor implements RatesProcessorInterface
{
    /**
     * @var Extractor
     */
    private $rateRequestExtractor;

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
     * @param Extractor $rateRequestExtractor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QuoteCollectionPointRepositoryInterface $collectionPointRepository
     * @param ShippingExperienceInterfaceFactory $shippingExperienceFactory
     */
    public function __construct(
        Extractor $rateRequestExtractor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuoteCollectionPointRepositoryInterface $collectionPointRepository,
        ShippingExperienceInterfaceFactory $shippingExperienceFactory
    ) {
        $this->rateRequestExtractor = $rateRequestExtractor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionPointRepository = $collectionPointRepository;
        $this->shippingExperienceFactory = $shippingExperienceFactory;
    }

    /**
     * Extract collection point shipping experiences from response.
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
        $collectionPointId = $requestType->getCollectionPoint()->getCollectionPointId();
        if (!$collectionPointId) {
            // no selected collection point in request
            return [];
        }

        $shippingAddress = $this->rateRequestExtractor->getShippingAddress($rateRequest);
        $this->searchCriteriaBuilder->addFilter(
            QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID,
            $shippingAddress->getId()
        );
        $this->searchCriteriaBuilder->addFilter(
            QuoteCollectionPointInterface::COLLECTION_POINT_ID,
            $collectionPointId
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
