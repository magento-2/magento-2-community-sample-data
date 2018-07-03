<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipping;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\Shipment\TrackEventInterface;
use Temando\Shipping\Model\Shipping\RateRequest\RequestDataInitializer;
use Temando\Shipping\Webservice\Processor\OrderOperationProcessorPool;

/**
 * Temando Shipping Carrier
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var StatusFactory
     */
    private $trackStatusFactory;

    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var RequestDataInitializer
     */
    private $requestDataInitializer;

    /**
     * @var OrderOperationProcessorPool
     */
    private $processorPool;

    /**
     * Carrier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param StatusFactory $trackStatusFactory
     * @param MethodFactory $rateMethodFactory
     * @param ResultFactory $rateResultFactory
     * @param ManagerInterface $messageManager
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param RequestDataInitializer $ratesRequestDataInitializer
     * @param OrderOperationProcessorPool $processorPool
     * @param mixed[] $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        StatusFactory $trackStatusFactory,
        MethodFactory $rateMethodFactory,
        ResultFactory $rateResultFactory,
        ManagerInterface $messageManager,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository,
        RequestDataInitializer $ratesRequestDataInitializer,
        OrderOperationProcessorPool $processorPool,
        array $data = []
    ) {
        $this->trackStatusFactory = $trackStatusFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->rateResultFactory = $rateResultFactory;
        $this->messageManager = $messageManager;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderRepository = $orderRepository;
        $this->requestDataInitializer = $ratesRequestDataInitializer;
        $this->processorPool = $processorPool;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Code of the carrier
     */
    const CODE = 'temando';

    /**
     * Carrier's code
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Fetch shipping rates from API.
     *
     * @param RateRequest $rateRequest
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(RateRequest $rateRequest)
    {
        $result = $this->rateResultFactory->create();

        $activeFlag = $this->getData('active_flag');
        if ($activeFlag && !$this->getConfigFlag($activeFlag)) {
            return $result;
        }

        try {
            // create request data from rate request
            $order = $this->requestDataInitializer->getQuotingData($rateRequest);
            // send order to Temando platform, will respond with shipping options
            $saveResult = $this->orderRepository->save($order);
            // read applicable shipping options from response
            $shippingOptions = $this->processorPool->processRatesResponse($rateRequest, $order, $saveResult);
        } catch (LocalizedException $e) {
            $this->_logger->log(LogLevel::WARNING, $e->getMessage(), ['exception' => $e]);
            $shippingOptions = [];
        }

        foreach ($shippingOptions as $shippingOption) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($shippingOption->getCode());
            $method->setMethodTitle($shippingOption->getLabel());

            $method->setPrice($shippingOption->getCost());
            $method->setCost($shippingOption->getCost());

            $result->append($method);
        }

        if (empty($result->getAllRates())) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $error = $this->_rateErrorFactory->create(['data' => [
                'carrier' => $this->_code,
                'carrier_title' => $this->getConfigData('title'),
                'error_message' => $this->getConfigData('specificerrmsg'),
            ]]);
            $result->append($error);
        }

        return $result;
    }

    /**
     * The Temando shipping carrier does not introduce static/generic/offline methods.
     *
     * @return mixed[]
     */
    public function getAllowedMethods()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Get tracking information. Original return value annotation is misleading.
     *
     * @see \Magento\Shipping\Model\Carrier\AbstractCarrier::isTrackingAvailable()
     * @see \Magento\Shipping\Model\Carrier\AbstractCarrierOnline::getTrackingInfo()
     * @see \Magento\Dhl\Model\Carrier::getTracking()
     * @param string $trackingNumber
     * @return \Magento\Shipping\Model\Tracking\Result\AbstractResult
     */
    public function getTrackingInfo($trackingNumber)
    {
        /** @var \Magento\Shipping\Model\Tracking\Result\Status $tracking */
        $tracking = $this->trackStatusFactory->create();
        $tracking->setCarrier($this->_code);
        $tracking->setTracking($trackingNumber);

        $shipmentTrack = $this->shipmentRepository->getShipmentTrack($this->_code, $trackingNumber);
        $carrierTitle = $shipmentTrack->getTitle() ? $shipmentTrack->getTitle() : $this->getConfigData('title');
        $tracking->setCarrierTitle($carrierTitle);

        try {
            $trackEvents = $this->shipmentRepository->getTrackingByNumber($trackingNumber);
            $trackEventsData = array_map(function (TrackEventInterface $trackEvent) {
                return $trackEvent->getEventData();
            }, $trackEvents);

            $tracking->setStatus(isset($trackEventsData[0]) ? $trackEventsData[0]['activity'] : '');
            $tracking->setProgressdetail($trackEventsData);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        try {
            $shipmentReference = $this->shipmentRepository->getReferenceByTrackingNumber($trackingNumber);
            $tracking->setUrl($shipmentReference->getExtTrackingUrl());
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $tracking;
    }
}
