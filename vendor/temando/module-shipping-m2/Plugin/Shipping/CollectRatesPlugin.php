<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Shipping;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\RateCollectorInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Model\Checkout\RateRequest\Extractor;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Repository\CollectionPointSearchRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\PickupLocationSearchRepositoryInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * CollectRatesPlugin
 *
 * @package Temando\Shipping\Plugin
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectRatesPlugin
{
    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * @var Extractor
     */
    private $rateRequestExtractor;

    /**
     * @var CollectionPointSearchRepositoryInterface
     */
    private $collectionPointSearchRequestRepository;

    /**
     * @var PickupLocationSearchRepositoryInterface
     */
    private $pickupLocationSearchRequestRepository;

    /**
     * CollectRatesPlugin constructor.
     * @param ModuleConfigInterface $moduleConfig
     * @param Extractor $rateRequestExtractor
     * @param CollectionPointSearchRepositoryInterface $collectionPointSearchRequestRepository
     * @param PickupLocationSearchRepositoryInterface $pickupLocationSearchRequestRepository
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        Extractor $rateRequestExtractor,
        CollectionPointSearchRepositoryInterface $collectionPointSearchRequestRepository,
        PickupLocationSearchRepositoryInterface $pickupLocationSearchRequestRepository
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->rateRequestExtractor = $rateRequestExtractor;
        $this->collectionPointSearchRequestRepository = $collectionPointSearchRequestRepository;
        $this->pickupLocationSearchRequestRepository = $pickupLocationSearchRequestRepository;
    }

    /**
     * Check if collection point delivery option was chosen.
     *
     * @param int $addressId
     * @return bool
     */
    private function isCollectionPointDelivery($addressId)
    {
        try {
            $collectionPointSearch = $this->collectionPointSearchRequestRepository->get($addressId);
            $isCollectionPointDelivery = (bool) $collectionPointSearch->getShippingAddressId();
        } catch (NoSuchEntityException $exception) {
            $isCollectionPointDelivery = false;
        }

        return $isCollectionPointDelivery;
    }

    /**
     * Check if store pickup delivery option was chosen.
     *
     * @param int $addressId
     * @return bool
     */
    private function isPickupLocationDelivery($addressId)
    {
        try {
            $pickupLocationSearch = $this->pickupLocationSearchRequestRepository->get($addressId);
            $isPickupLocationDelivery = (bool) $pickupLocationSearch->getShippingAddressId();
        } catch (NoSuchEntityException $exception) {
            $isPickupLocationDelivery = false;
        }

        return $isPickupLocationDelivery;
    }

    /**
     * Disable other carriers if current shipping destination is
     * a collection point or store pickup location.
     *
     * @param RateCollectorInterface $subject
     * @param RateRequest $rateRequest
     * @return null
     */
    public function beforeCollectRates(RateCollectorInterface $subject, RateRequest $rateRequest)
    {
        try {
            $quote = $this->rateRequestExtractor->getQuote($rateRequest);
            $shippingAddress = $this->rateRequestExtractor->getShippingAddress($rateRequest);
        } catch (LocalizedException $exception) {
            return null;
        }

        $isCheckoutEnabled = $this->moduleConfig->isEnabled($quote->getStoreId());
        $isDeliveryLocationEnabled = $this->moduleConfig->isCollectionPointsEnabled($quote->getStoreId())
            || $this->moduleConfig->isClickAndCollectEnabled($quote->getStoreId());

        if (!$isCheckoutEnabled || !$isDeliveryLocationEnabled) {
            // certainly no collection point or click&collect delivery
            return null;
        }

        $addressId = $shippingAddress->getId();
        $isCollectionPointDelivery = $this->isCollectionPointDelivery($addressId);
        $isPickupLocationDelivery = $this->isPickupLocationDelivery($addressId);

        if ($isCollectionPointDelivery || $isPickupLocationDelivery) {
            $rateRequest->setLimitCarrier(Carrier::CODE);
        }

        return null;
    }
}
