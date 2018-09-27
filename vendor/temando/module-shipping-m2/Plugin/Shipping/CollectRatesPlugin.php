<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Shipping;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\RateCollectorInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Repository\CollectionPointSearchRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\QuoteCollectionPointRepositoryInterface;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\Model\Shipping\RateRequest\Extractor;

/**
 * CollectRatesPlugin
 *
 * @package Temando\Shipping\Plugin
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
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
    private $searchRequestRepository;

    /**
     * @var QuoteCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * CollectRatesPlugin constructor.
     * @param ModuleConfigInterface $moduleConfig
     * @param Extractor $rateRequestExtractor
     * @param CollectionPointSearchRepositoryInterface $searchRequestRepository
     * @param QuoteCollectionPointRepositoryInterface $collectionPointRepository
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        Extractor $rateRequestExtractor,
        CollectionPointSearchRepositoryInterface $searchRequestRepository,
        QuoteCollectionPointRepositoryInterface $collectionPointRepository
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->rateRequestExtractor = $rateRequestExtractor;
        $this->searchRequestRepository = $searchRequestRepository;
        $this->collectionPointRepository = $collectionPointRepository;
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
            $collectionPointSearch = $this->searchRequestRepository->get($addressId);
            $isCollectionPointDelivery = (bool) $collectionPointSearch->getShippingAddressId();
        } catch (NoSuchEntityException $exception) {
            $isCollectionPointDelivery = false;
        }

        return $isCollectionPointDelivery;
    }

    /**
     * Check if a collection point was selected for quoting (rates request)
     *
     * @param int $addressId
     * @return bool
     */
    private function isCollectionPointRequest($addressId)
    {
        try {
            $collectionPoint = $this->collectionPointRepository->getSelected($addressId);
            $isCollectionPointRequest = (bool) $collectionPoint->getEntityId();
        } catch (NoSuchEntityException $exception) {
            $isCollectionPointRequest = false;
        }

        return $isCollectionPointRequest;
    }

    /**
     * Disable other carriers if current shipping destination is a collection point.
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

        if (!$this->moduleConfig->isEnabled($quote->getStoreId())
            || !$this->moduleConfig->isCollectionPointsEnabled($quote->getStoreId())
        ) {
            // certainly no collection point delivery
            return null;
        }

        $addressId = $shippingAddress->getId();
        $isCollectionPointDelivery = $this->isCollectionPointDelivery($addressId);
        $isCollectionPointRequest = $this->isCollectionPointRequest($addressId);

        if ($isCollectionPointDelivery || $isCollectionPointRequest) {
            $rateRequest->setLimitCarrier(Carrier::CODE);
        }

        return null;
    }
}
