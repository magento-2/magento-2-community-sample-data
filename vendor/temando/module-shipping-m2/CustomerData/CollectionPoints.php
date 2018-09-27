<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface;
use Temando\Shipping\Model\CollectionPoint\CartCollectionPointManagement;
use Temando\Shipping\Model\CollectionPoint\OpeningHoursFormatter;
use Temando\Shipping\Model\CollectionPoint\QuoteCollectionPoint;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Repository\CollectionPointSearchRepositoryInterface;

/**
 * CollectionPoints
 *
 * @package  Temando\Shipping\CustomerData
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CollectionPoints implements SectionSourceInterface
{
    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * @var SessionManagerInterface|\Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var CartCollectionPointManagement
     */
    private $cartCollectionPointManagement;

    /**
     * @var OpeningHoursFormatter
     */
    private $openingHoursFormatter;

    /**
     * @var CollectionPointSearchRepositoryInterface
     */
    private $searchRequestRepository;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * CollectionPoints constructor.
     * @param ModuleConfigInterface $moduleConfig
     * @param SessionManagerInterface $checkoutSession
     * @param CartCollectionPointManagement $cartCollectionPointManagement
     * @param OpeningHoursFormatter $openingHoursFormatter
     * @param CollectionPointSearchRepositoryInterface $searchRequestRepository
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        SessionManagerInterface $checkoutSession,
        CartCollectionPointManagement $cartCollectionPointManagement,
        OpeningHoursFormatter $openingHoursFormatter,
        CollectionPointSearchRepositoryInterface $searchRequestRepository,
        HydratorInterface $hydrator
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->cartCollectionPointManagement = $cartCollectionPointManagement;
        $this->searchRequestRepository = $searchRequestRepository;
        $this->openingHoursFormatter = $openingHoursFormatter;
        $this->checkoutSession = $checkoutSession;
        $this->hydrator = $hydrator;
    }

    /**
     * Obtain collection points data for display in checkout, shipping method step.
     *
     * @return string[]
     */
    public function getSectionData()
    {
        if (!$this->moduleConfig->isEnabled() || !$this->moduleConfig->isCollectionPointsEnabled()) {
            return [
                'collection-points' => [],
                'search-request' => [],
                'message' => ''
            ];
        }

        $quote = $this->checkoutSession->getQuote();
        $quoteAddressId = $quote->getShippingAddress()->getId();

        // check if customer checks out with collection points delivery option
        try {
            // a search request was performed or is pending (waiting for search input)
            $searchRequest = $this->searchRequestRepository->get($quoteAddressId);
            $searchRequestData = $this->hydrator->extract($searchRequest);
        } catch (LocalizedException $e) {
            // no search request found at all for given address
            $searchRequestData = [];
        }

        if (empty($searchRequestData) || !empty($searchRequestData['pending'])) {
            return [
                'collection-points' => [],
                'search-request' => $searchRequestData,
                'message' => __('Enter country and postal code to search for a collection point.')
            ];
        }

        $collectionPoints = $this->cartCollectionPointManagement->getCollectionPoints($quote->getId());

        // map collection points to data array with formatted/localized opening hours
        $collectionPoints = array_map(function (QuoteCollectionPointInterface $collectionPoint) {
            /** @var QuoteCollectionPoint $collectionPoint */
            $collectionPointData = $collectionPoint->toArray();

            $openingHours = $this->openingHoursFormatter->format($collectionPoint->getOpeningHours());
            $collectionPointData[QuoteCollectionPointInterface::OPENING_HOURS] = $openingHours;

            return $collectionPointData;
        }, $collectionPoints);

        $message = !empty($collectionPoints)
            ? __('There were %1 results for your search.', count($collectionPoints))
            : $message = __('No Collection Points found.');

        return [
            'collection-points' => array_values($collectionPoints),
            'search-request' => $searchRequestData,
            'message' => $message
        ];
    }
}
