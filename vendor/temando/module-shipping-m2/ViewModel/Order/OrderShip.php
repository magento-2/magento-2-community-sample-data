<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Order;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Model\Pickup\PickupLoader;
use Temando\Shipping\Model\ResourceModel\Repository\OrderCollectionPointRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\ViewModel\CoreApiInterface;
use Temando\Shipping\ViewModel\DataProvider\CoreApiAccess;
use Temando\Shipping\ViewModel\DataProvider\CoreApiAccessInterface;
use Temando\Shipping\ViewModel\DataProvider\DeliveryType;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\OrderShipInterface;
use Temando\Shipping\ViewModel\Pickup\PickupItems;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * View model for order ship JS component.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderShip implements ArgumentInterface, CoreApiInterface, OrderShipInterface, ShippingApiInterface
{
    /**
     * @var CoreApiAccess
     */
    private $coreApiAccess;

    /**
     * @var ShippingApiAccess
     */
    private $shippingApiAccess;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var DeliveryType
     */
    private $deliveryType;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * @var OrderCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var PickupLoader
     */
    private $pickupLoader;

    /**
     * @var PickupItems
     */
    private $pickupItems;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderShip constructor.
     * @param CoreApiAccess $coreApiAccess
     * @param ShippingApiAccess $shippingApiAccess
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $serializer
     * @param DeliveryType $deliveryType
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipmentProviderInterface $shipmentProvider
     * @param OrderCollectionPointRepositoryInterface $collectionPointRepository
     * @param PickupLoader $pickupLoader
     * @param PickupItems $pickupItems
     * @param LoggerInterface $logger
     */
    public function __construct(
        CoreApiAccess $coreApiAccess,
        ShippingApiAccess $shippingApiAccess,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig,
        Json $serializer,
        DeliveryType $deliveryType,
        OrderRepositoryInterface $orderRepository,
        ShipmentProviderInterface $shipmentProvider,
        OrderCollectionPointRepositoryInterface $collectionPointRepository,
        PickupLoader $pickupLoader,
        PickupItems $pickupItems,
        LoggerInterface $logger
    ) {
        $this->coreApiAccess = $coreApiAccess;
        $this->shippingApiAccess = $shippingApiAccess;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->deliveryType = $deliveryType;
        $this->orderRepository = $orderRepository;
        $this->shipmentProvider = $shipmentProvider;
        $this->collectionPointRepository = $collectionPointRepository;
        $this->pickupLoader = $pickupLoader;
        $this->pickupItems = $pickupItems;
        $this->logger = $logger;
    }

    /**
     * Obtain order properties for JSON serialization.
     *
     * @return string[]
     */
    private function getOrderProperties(): array
    {
        return [
            'entity_id',
            'increment_id',
            'is_virtual',
            'store_id',
            'customer_id',
            'base_shipping_amount',
            'customer_is_guest',
            'billing_address_id',
            'shipping_address_id',
            'weight',
            'total_qty_ordered',
            'base_currency_code'
        ];
    }

    /**
     * Obtain order item properties for JSON serialization.
     *
     * @return string[]
     */
    private function getOrderItemProperties(): array
    {
        return [
            'item_id',
            'order_id',
            'store_id',
            'product_id',
            'weight',
            'is_virtual',
            'sku',
            'name',
            'qty_ordered',
            'qty_shipped',
            'base_price',
            'base_row_total'
        ];
    }

    /**
     * Obtain order address properties for JSON serialization.
     *
     * @return string[]
     */
    private function getOrderAddressProperties(): array
    {
        return [
            'entity_id',
            'postcode',
            'lastname',
            'street',
            'city',
            'email',
            'telephone',
            'country_id',
            'firstname',
            'address_type',
            'prefix',
            'middlename',
            'suffix',
            'company'
        ];
    }

    /**
     * @return OrderInterface|\Magento\Sales\Model\Order
     */
    private function getOrder(): OrderInterface
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->shipmentProvider->getSalesShipment();
        $order = $shipment->getOrder();
        return $order;
    }

    /**
     * Determine the quantity to be excluded from shipments, e.g. due to them
     * being already prepared for store pickup.
     *
     * @param string $itemSku
     * @return int
     */
    private function getReservedQty(string $itemSku): int
    {
        $order = $this->getOrder();

        if (!$this->deliveryType->isPickupOrder($order)) {
            return 0;
        }

        $orderId = (int)$order->getEntityId();
        $pickups = $this->pickupLoader->load($orderId);
        $this->pickupLoader->register($pickups, $orderId);

        return $this->pickupItems->getQtyPrepared($itemSku);
    }

    /**
     * @return CoreApiAccessInterface
     */
    public function getCoreApiAccess(): CoreApiAccessInterface
    {
        return $this->coreApiAccess;
    }

    /**
     * @return ShippingApiAccessInterface
     */
    public function getShippingApiAccess(): ShippingApiAccessInterface
    {
        return $this->shippingApiAccess;
    }

    /**
     * Obtain Magento REST API endpoint for shipment creation.
     *
     * @return string
     */
    public function getShipEndpoint(): string
    {
        $orderId = $this->getOrder()->getId();
        $endpoint = $this->urlBuilder->getDirectUrl("rest/V1/order/$orderId/ship", ['_secure' => true]);

        // core bug workaround, route parameter "_direct" does not get reset
        $this->urlBuilder->getUrl("rest/V1/order/$orderId/ship", ['_direct' => null]);

        return $endpoint;
    }

    /**
     * Obtain a JSON representation of relevant order data for usage in the
     * OrderShip UI component.
     *
     * @return string
     */
    public function getOrderData(): string
    {
        $order = $this->getOrder();

        $orderItems = [];
        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getIsVirtual() && !$orderItem->getParentItem()) {
                // skip virtual and child items
                $qtyShipped = $orderItem->getQtyShipped() + $this->getReservedQty($orderItem->getSku());

                $orderItems[$orderItem->getId()] = $orderItem->toArray($this->getOrderItemProperties());
                $orderItems[$orderItem->getId()]['qty_shipped'] = number_format($qtyShipped, 3);
            }
        }

        $orderAddresses = [];
        /** @var \Magento\Sales\Model\Order\Address $orderAddress */
        foreach ($order->getAddresses() as $orderAddress) {
            $orderAddresses[$orderAddress->getId()] = $orderAddress->toArray($this->getOrderAddressProperties());
            $orderAddresses[$orderAddress->getId()]['region'] = $orderAddress->getRegionCode();
        }

        $orderData = $order->toArray($this->getOrderProperties());
        $orderData['items'] = $orderItems;
        $orderData['addresses'] = $orderAddresses;

        try {
            $collectionPoint = $this->collectionPointRepository->get($orderData['shipping_address_id']);
            $orderData['final_recipient_address_id'] = $orderData['shipping_address_id'];
            $orderData['shipping_address_id'] = $collectionPoint->getCollectionPointId();
            $orderData['addresses'][$collectionPoint->getCollectionPointId()] = [
                'entity_id' => $collectionPoint->getCollectionPointId(),
                'postcode' => $collectionPoint->getPostcode(),
                'street' => implode("\n", $collectionPoint->getStreet()),
                'city' => $collectionPoint->getCity(),
                'country_id' => $collectionPoint->getCountry(),
                'address_type' => 'collection_point',
                'company' => $collectionPoint->getName(),
                'region' => $collectionPoint->getRegion(),
            ];
        } catch (LocalizedException $exception) {
            $orderData['final_recipient_address_id'] = $orderData['shipping_address_id'];
        }

        return $this->serializer->serialize($orderData);
    }

    /**
     * Obtain a JSON representation of relevant order metadata for usage in the
     * OrderShip UI component.
     *
     * @return string
     */
    public function getOrderMeta(): string
    {
        $order = $this->getOrder();
        $shippingAddressId = $order->getShippingAddress()->getId();

        try {
            $collectionPoint = $this->collectionPointRepository->get($shippingAddressId);
            $isCollectionPoint = (bool) $collectionPoint->getRecipientAddressId();
        } catch (LocalizedException $e) {
            $isCollectionPoint = false;
        }

        $orderMeta = ['isCollectionPoint' => $isCollectionPoint];

        return $this->serializer->serialize($orderMeta);
    }

    /**
     * @return string
     */
    public function getSelectedExperience(): string
    {
        $order = $this->getOrder();
        $shippingMethod = $order->getShippingMethod(true);
        $experienceCode = $shippingMethod->getData('method');

        return $experienceCode;
    }

    /**
     * @return string
     */
    public function getExtOrderId(): string
    {
        $order = $this->getOrder();

        try {
            $orderReference = $this->orderRepository->getReferenceByOrderId($order->getId());
            $extOrderId = $orderReference->getExtOrderId();
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $extOrderId = '';
        }

        return $extOrderId;
    }

    /**
     * @return string
     */
    public function getShipmentViewPageUrl(): string
    {
        return $this->urlBuilder->getUrl('sales/shipment/view', ['shipment_id' => '--id--']);
    }

    /**
     * @return string
     */
    public function getDefaultCurrency(): string
    {
        $order = $this->getOrder();
        return $order->getBaseCurrencyCode();
    }

    /**
     * @return string
     */
    public function getDefaultDimensionsUnit(): string
    {
        $weightUnit = $this->getDefaultWeightUnit();
        if ($weightUnit === 'lbs') {
            return 'in';
        }

        return 'cm';
    }

    /**
     * @return string
     */
    public function getDefaultWeightUnit(): string
    {
        $order = $this->getOrder();
        $weightUnit = $this->scopeConfig->getValue(
            DirectoryHelper::XML_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE,
            $order->getStore()->getCode()
        );

        return $weightUnit;
    }

    /**
     * @return string
     */
    public function getConfigUrl(): string
    {
        return $this->urlBuilder->getUrl('adminhtml/system_config/edit', [
            'section' => 'carriers',
            '_fragment' => 'carriers_temando-link',
        ]);
    }

    /**
     * Check if a shipment was registered for component rendering.
     * @see \Temando\Shipping\Plugin\Shipping\Order\ShipmentLoaderPlugin::afterLoad
     *
     * @return bool
     */
    public function hasSalesShipment(): bool
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        return (bool) $this->shipmentProvider->getSalesShipment();
    }
}
