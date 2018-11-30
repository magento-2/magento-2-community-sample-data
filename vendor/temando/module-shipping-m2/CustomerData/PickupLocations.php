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
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\Delivery\CartPickupLocationManagement;
use Temando\Shipping\Model\Delivery\OpeningHoursFormatter;
use Temando\Shipping\Model\Delivery\QuotePickupLocation;
use Temando\Shipping\Model\ResourceModel\Repository\PickupLocationSearchRepositoryInterface;

/**
 * PickupLocations
 *
 * @package Temando\Shipping\CustomerData
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupLocations implements SectionSourceInterface
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
     * @var CartPickupLocationManagement
     */
    private $cartPickupLocationManagement;

    /**
     * @var OpeningHoursFormatter
     */
    private $openingHoursFormatter;

    /**
     * @var PickupLocationSearchRepositoryInterface
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
     * PickupLocations constructor.
     * @param ModuleConfigInterface $moduleConfig
     * @param SessionManagerInterface $checkoutSession
     * @param CartPickupLocationManagement $cartPickupLocationManagement
     * @param OpeningHoursFormatter $openingHoursFormatter
     * @param PickupLocationSearchRepositoryInterface $searchRequestRepository
     * @param StoreManagerInterface $storeManager
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        SessionManagerInterface $checkoutSession,
        CartPickupLocationManagement $cartPickupLocationManagement,
        OpeningHoursFormatter $openingHoursFormatter,
        PickupLocationSearchRepositoryInterface $searchRequestRepository,
        StoreManagerInterface $storeManager,
        HydratorInterface $hydrator
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->cartPickupLocationManagement = $cartPickupLocationManagement;
        $this->openingHoursFormatter = $openingHoursFormatter;
        $this->checkoutSession = $checkoutSession;
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

        if (!$this->moduleConfig->isEnabled($storeId) || !$this->moduleConfig->isClickAndCollectEnabled($storeId)) {
            return [
                'pickup-locations' => [],
                'search-request' => []
            ];
        }

        $quote = $this->checkoutSession->getQuote();
        $quoteAddressId = $quote->getShippingAddress()->getId();

        try {
            $searchRequest = $this->searchRequestRepository->get($quoteAddressId);
            $searchRequest = $this->hydrator->extract($searchRequest);
            $pickupLocations = $this->cartPickupLocationManagement->getPickupLocations($quote->getId());
        } catch (LocalizedException $e) {
            $searchRequest = [];
            $pickupLocations = [];
        }

        // map pickup locations to data array with formatted/localized opening hours
        $pickupLocations = array_map(function (QuotePickupLocationInterface $pickupLocation) {
            /** @var QuotePickupLocation $pickupLocation */
            $pickupLocationData = $pickupLocation->toArray();

            $openingHours = $this->openingHoursFormatter->format($pickupLocation->getOpeningHours());
            $pickupLocationData[QuotePickupLocationInterface::OPENING_HOURS] = $openingHours;

            return $pickupLocationData;
        }, $pickupLocations);

        return [
            'pickup-locations' => array_values($pickupLocations),
            'search-request' => $searchRequest
        ];
    }
}
