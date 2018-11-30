<?php
/**
 * This file is part of the Klarna Order Management module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Ordermanagement\Model\Api;

use Klarna\Core\Helper\ConfigHelper;
use Klarna\Core\Helper\DataConverter;
use Klarna\Core\Helper\KlarnaConfig;
use Klarna\Core\Model\Api\BuilderFactory;
use Klarna\Ordermanagement\Api\ApiInterface;
use Klarna\Ordermanagement\Model\Api\Rest\Service\Ordermanagement as OrdermanagementApi;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class Ordermanagement
 *
 * @package Klarna\Ordermanagement\Model\Api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Ordermanagement implements ApiInterface
{
    /**
     * Order fraud statuses
     */
    const ORDER_FRAUD_STATUS_ACCEPTED = 'ACCEPTED';
    const ORDER_FRAUD_STATUS_REJECTED = 'REJECTED';
    const ORDER_FRAUD_STATUS_PENDING  = 'PENDING';

    const RET_ORDER_FRAUD_STATUS_ACCEPTED = 1;
    const RET_ORDER_FRAUD_STATUS_REJECTED = -1;
    const RET_ORDER_FRAUD_STATUS_PENDING  = 0;

    /**
     * Order notification statuses
     */
    const ORDER_NOTIFICATION_FRAUD_REJECTED = 'FRAUD_RISK_REJECTED';
    const ORDER_NOTIFICATION_FRAUD_ACCEPTED = 'FRAUD_RISK_ACCEPTED';
    const ORDER_NOTIFICATION_FRAUD_STOPPED  = 'FRAUD_RISK_STOPPED';

    /**
     * API allowed shipping method code
     */
    const KLARNA_API_SHIPPING_METHOD_HOME = "Home";
    const KLARNA_API_SHIPPING_METHOD_PICKUPSTORE = "PickUpStore";
    const KLARNA_API_SHIPPING_METHOD_BOXREG = "BoxReg";
    const KLARNA_API_SHIPPING_METHOD_BOXUNREG = "BoxUnreg";
    const KLARNA_API_SHIPPING_METHOD_PICKUPPOINT = "PickUpPoint";
    const KLARNA_API_SHIPPING_METHOD_OWN = "Own";

    /**
     * @var DataObject
     */
    private $klarnaOrder;
    /**
     * @var OrdermanagementApi
     */
    private $orderManagement;
    /**
     * @var ConfigHelper
     */
    private $helper;
    /**
     * @var BuilderFactory
     */
    private $builderFactory;
    /**
     * @var ManagerInterface
     */
    private $eventManager;
    /**
     * API type code
     *
     * @var string
     */
    private $builderType = '';
    /**
     * @var KlarnaConfig
     */
    private $klarnaConfig;
    /**
     * @var DataConverter
     */
    private $dataConverter;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * OrdermanagementApi constructor.
     *
     * @param OrdermanagementApi $orderManagement
     * @param ConfigHelper       $helper
     * @param KlarnaConfig       $klarnaConfig
     * @param DataConverter      $dataConverter
     * @param BuilderFactory     $builderFactory
     * @param ManagerInterface   $eventManager
     * @param DataObjectFactory  $dataObjectFactory
     * @param string             $builderType
     */
    public function __construct(
        OrdermanagementApi $orderManagement,
        ConfigHelper $helper,
        KlarnaConfig $klarnaConfig,
        DataConverter $dataConverter,
        BuilderFactory $builderFactory,
        ManagerInterface $eventManager,
        DataObjectFactory $dataObjectFactory,
        $builderType = ''
    ) {
        $this->orderManagement = $orderManagement;
        $this->helper = $helper;
        $this->builderFactory = $builderFactory;
        $this->eventManager = $eventManager;
        $this->builderType = $builderType;
        $this->klarnaConfig = $klarnaConfig;
        $this->dataConverter = $dataConverter;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Get the fraud status of an order to determine if it should be accepted or denied within Magento
     *
     * Return value of 1 means accept
     * Return value of 0 means still pending
     * Return value of -1 means deny
     *
     * @param string $orderId
     *
     * @return int
     */
    public function getFraudStatus($orderId)
    {
        $klarnaOrder = $this->orderManagement->getOrder($orderId);
        $klarnaOrder = $this->dataObjectFactory->create(['data' => $klarnaOrder]);
        switch ($klarnaOrder->getFraudStatus()) {
            case self::ORDER_FRAUD_STATUS_ACCEPTED:
                return self::RET_ORDER_FRAUD_STATUS_ACCEPTED;
            case self::ORDER_FRAUD_STATUS_REJECTED:
                return self::RET_ORDER_FRAUD_STATUS_REJECTED;
            case self::ORDER_FRAUD_STATUS_PENDING:
            default:
                return self::RET_ORDER_FRAUD_STATUS_PENDING;
        }
    }

    /**
     * Acknowledge an order in order management
     *
     * @param string $orderId
     *
     * @return DataObject
     */
    public function acknowledgeOrder($orderId)
    {
        $response = $this->orderManagement->acknowledgeOrder($orderId);
        $response = $this->dataObjectFactory->create(['data' => $response]);
        return $response;
    }

    /**
     * Update merchant references for a Klarna order
     *
     * @param string $orderId
     * @param string $reference1
     * @param string $reference2
     *
     * @return DataObject
     */
    public function updateMerchantReferences($orderId, $reference1, $reference2 = null)
    {
        $response = $this->orderManagement->updateMerchantReferences($orderId, $reference1, $reference2);
        $response = $this->dataObjectFactory->create(['data' => $response]);
        return $response;
    }

    /**
     * Capture an amount on an order
     *
     * @param string  $orderId
     * @param float   $amount
     * @param Invoice $invoice
     *
     * @return DataObject
     * @throws LocalizedException
     * @throws \Klarna\Core\Exception
     * @throws \Klarna\Core\Model\Api\Exception
     */
    public function capture($orderId, $amount, $invoice = null)
    {
        $data = [
            'captured_amount' => $this->dataConverter->toApiFloat($amount)
        ];
        $data = $this->prepareOrderLines($data, $invoice);
        $data = $this->checkShippingDelay($data);

        $response = $this->orderManagement->captureOrder($orderId, $data);
        $response = $this->dataObjectFactory->create(['data' => $response]);

        /**
         * If a capture fails, attempt to extend the auth and attempt capture again.
         * This work in certain cases that cannot be detected via api calls
         */
        if (!$response->getIsSuccessful()) {
            $extendResponse = $this->orderManagement->extendAuthorization($orderId);
            $extendResponse = $this->dataObjectFactory->create(['data' => $extendResponse]);

            if ($extendResponse->getIsSuccessful()) {
                $response = $this->orderManagement->captureOrder($orderId, $data);
                $response = $this->dataObjectFactory->create(['data' => $response]);
            }
        }

        if ($response->getIsSuccessful()) {
            $responseObject = $response->getResponseObject();
            $captureId = $this->orderManagement
                ->getLocationResourceId($responseObject['headers']['Location']);

            if ($captureId) {
                $captureDetails = $this->orderManagement->getCapture($orderId, $captureId);
                $captureDetails = $this->dataObjectFactory->create(['data' => $captureDetails]);

                if ($captureDetails->getKlarnaReference()) {
                    $captureDetails->setTransactionId($captureDetails->getKlarnaReference());
                }
                return $captureDetails;
            }
        }

        return $response;
    }

    /**
     * Add shipping info to capture
     *
     * @param string $orderId
     * @param string $captureId
     * @param array $shippingInfo
     * @return array|DataObject
     */
    public function addShippingInfo($orderId, $captureId, $shippingInfo)
    {
        $data = $this->prepareShippingInfo($shippingInfo);
        $response = $this->orderManagement->addShippingInfo($orderId, $captureId, $data);
        $response = $this->dataObjectFactory->create(['data' => $response]);
        return $response;
    }

    /**
     * Prepare shipping info
     *
     * @param array $shippingInfo
     * @return array
     */
    private function prepareShippingInfo(array $shippingInfo)
    {
        $data = [];
        foreach ($shippingInfo as $shipping) {
            $data['shipping_info'][] = [
                'tracking_number' => substr($shipping['number'], 0, 100),
                'shipping_method' => $this->getKlarnaShippingMethod($shipping),
                'shipping_company' => substr($shipping['title'], 0, 100)
            ];
        }

        return $data;
    }

    /**
     * Get Api Accepted shipping method,For merchant who implement this feature
     * Create Plugin to overwrite this default method code
     * Allowed values matches (PickUpStore|Home|BoxReg|BoxUnreg|PickUpPoint|Own)
     *
     * @param array $shipping
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getKlarnaShippingMethod(array $shipping)
    {
        return self::KLARNA_API_SHIPPING_METHOD_HOME;
    }


    /**
     * @param array                   $data
     * @param Invoice|Creditmemo|null $document
     * @return array
     * @throws LocalizedException
     * @throws \Klarna\Core\Exception
     */
    private function prepareOrderLines($data, $document = null)
    {
        /**
         * Get items for capture
         */
        if ($document instanceof Invoice || $document instanceof Creditmemo) {
            $orderItems = $this->getGenerator()
                ->setObject($document)
                ->collectOrderLines($document->getStore())
                ->getOrderLines($document->getStore(), true);

            if ($orderItems) {
                $data['order_lines'] = $orderItems;
            }
        }
        return $data;
    }

    /**
     * @return \Klarna\Core\Api\BuilderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getGenerator()
    {
        return $this->builderFactory->create($this->builderType);
    }

    /**
     * Set shipping delay for capture
     *
     * Change this setting when items will not be shipped for x amount of days after capture.
     * For instance, you capture on Friday but won't ship until Monday. A 3 day shipping delay would be set.
     *
     * @param array $data
     * @param int   $shippingDelay
     * @return array
     */
    public function checkShippingDelay($data, $shippingDelay = 0)
    {
        $shippingDelayObject = $this->dataObjectFactory->create(['data' => ['shipping_delay' => $shippingDelay]]);

        $this->eventManager->dispatch(
            'klarna_capture_shipping_delay',
            ['shipping_delay_object' => $shippingDelayObject]
        );

        if ($shippingDelayObject->getShippingDelay()) {
            $data['shipping_delay'] = $shippingDelayObject->getShippingDelay();
        }
        return $data;
    }

    /**
     * Refund for an order
     *
     * @param string     $orderId
     * @param float      $amount
     * @param Creditmemo $creditMemo
     *
     * @return DataObject
     * @throws \Klarna\Core\Exception
     * @throws LocalizedException
     */
    public function refund($orderId, $amount, $creditMemo = null)
    {
        $data = [
            'refunded_amount' => $this->dataConverter->toApiFloat($amount)
        ];

        if (!is_null($creditMemo->getCustomerNote())) {
            $data['description'] = $creditMemo->getCustomerNote();
        }

        $data = $this->prepareOrderLines($data, $creditMemo);

        $response = $this->orderManagement->refund($orderId, $data);
        $response = $this->dataObjectFactory->create(['data' => $response]);

        if ($response->getIsSuccessful()) {
            $response->setTransactionId($this->orderManagement->getLocationResourceId($response));
        }
        return $response;
    }

    /**
     * Cancel an order
     *
     * @param string $orderId
     *
     * @return DataObject
     */
    public function cancel($orderId)
    {
        $response = $this->orderManagement->cancelOrder($orderId);
        $response = $this->dataObjectFactory->create(['data' => $response]);
        return $response;
    }

    /**
     * Release the authorization on an order
     *
     * @param string $orderId
     *
     * @return DataObject
     */
    public function release($orderId)
    {
        $response = $this->orderManagement->releaseAuthorization($orderId);
        $response = $this->dataObjectFactory->create(['data' => $response]);
        return $response;
    }

    /**
     * Get order details for a completed Klarna order
     *
     * @param string $orderId
     *
     * @return DataObject
     */
    public function getPlacedKlarnaOrder($orderId)
    {
        $response = $this->orderManagement->getOrder($orderId);
        $response = $this->dataObjectFactory->create(['data' => $response]);
        return $response;
    }

    /**
     * Get Klarna Checkout Reservation Id
     *
     * @return string
     */
    public function getReservationId()
    {
        return $this->getKlarnaOrder()->getOrderId();
    }

    /**
     * Get Klarna checkout order details
     *
     * @return DataObject
     */
    public function getKlarnaOrder()
    {
        if ($this->klarnaOrder === null) {
            $this->klarnaOrder = $this->dataObjectFactory->create();
        }

        return $this->klarnaOrder;
    }

    /**
     * Set Klarna checkout order details
     *
     * @param DataObject $klarnaOrder
     *
     * @return $this
     */
    public function setKlarnaOrder(DataObject $klarnaOrder)
    {
        $this->klarnaOrder = $klarnaOrder;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws \Klarna\Core\Exception
     */
    public function resetForStore($store, $methodCode)
    {
        $versionConfig = $this->klarnaConfig->getVersionConfig($store);
        $this->setBuilderType($this->klarnaConfig->getOmBuilderType($versionConfig, $methodCode));
        $user = $this->helper->getApiConfig('merchant_id', $store);
        $password = $this->helper->getApiConfig('shared_secret', $store);
        $test_mode = $this->helper->isApiConfigFlag('test_mode', $store);
        $url = $versionConfig->getUrl($test_mode);
        $this->orderManagement->resetForStore($user, $password, $url);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setBuilderType($builderType)
    {
        $this->builderType = $builderType;
        return $this;
    }
}
