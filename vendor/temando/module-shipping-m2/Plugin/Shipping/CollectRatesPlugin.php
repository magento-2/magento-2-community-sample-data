<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Shipping;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\RateCollectorInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
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
     * @var Extractor
     */
    private $rateRequestExtractor;

    /**
     * @var QuoteCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * CollectRatesPlugin constructor.
     * @param Extractor $rateRequestExtractor
     * @param QuoteCollectionPointRepositoryInterface $collectionPointRepository
     */
    public function __construct(
        Extractor $rateRequestExtractor,
        QuoteCollectionPointRepositoryInterface $collectionPointRepository
    ) {
        $this->rateRequestExtractor = $rateRequestExtractor;
        $this->collectionPointRepository = $collectionPointRepository;
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
        $shippingAddress = $this->rateRequestExtractor->getShippingAddress($rateRequest);
        $addressId = $shippingAddress->getId();

        try {
            $collectionPoint = $this->collectionPointRepository->getSelected($addressId);
            $isCollectionPointRequest = (bool) $collectionPoint->getEntityId();
        } catch (NoSuchEntityException $exception) {
            $isCollectionPointRequest = false;
        }

        if ($isCollectionPointRequest) {
            $rateRequest->setLimitCarrier(Carrier::CODE);
        }

        return null;
    }
}
