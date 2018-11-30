<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Api\Data\Delivery\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Delivery\CartCollectionPointManagementInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\Delivery\OpeningHoursFormatter;
use Temando\Shipping\Model\Delivery\QuoteCollectionPoint;
use Temando\Shipping\Model\ResourceModel\Repository\CollectionPointSearchRepositoryInterface;

/**
 * CollectionPoints
 *
 * @package Temando\Shipping\CustomerData
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
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
     * @var CartCollectionPointManagementInterface
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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * CollectionPoints constructor.
     * @param ModuleConfigInterface $moduleConfig
     * @param SessionManagerInterface $checkoutSession
     * @param CartCollectionPointManagementInterface $cartCollectionPointManagement
     * @param OpeningHoursFormatter $openingHoursFormatter
     * @param CollectionPointSearchRepositoryInterface $searchRequestRepository
     * @param StoreManagerInterface $storeManager
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        SessionManagerInterface $checkoutSession,
        CartCollectionPointManagementInterface $cartCollectionPointManagement,
        OpeningHoursFormatter $openingHoursFormatter,
        CollectionPointSearchRepositoryInterface $searchRequestRepository,
        StoreManagerInterface $storeManager,
        HydratorInterface $hydrator
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->checkoutSession = $checkoutSession;
        $this->cartCollectionPointManagement = $cartCollectionPointManagement;
        $this->openingHoursFormatter = $openingHoursFormatter;
        $this->searchRequestRepository = $searchRequestRepository;
        $this->storeManager = $storeManager;
        $this->hydrator = $hydrator;
    }

    /**
     * Obtain collection points data for display in checkout, shipping method step.
     *
     * @return string[]
     */
    public function getSectionData()
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $exception) {
            $storeId = null;
        }

        if (!$this->moduleConfig->isEnabled($storeId) || !$this->moduleConfig->isCollectionPointsEnabled($storeId)) {
            return [
                'collection-points' => [],
                'search-request' => [],
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

        return [
            'collection-points' => array_values($collectionPoints),
            'search-request' => $searchRequestData,
        ];
    }
}
