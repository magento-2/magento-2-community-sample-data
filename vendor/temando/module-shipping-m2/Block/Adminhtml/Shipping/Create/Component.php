<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Shipping\Create;

use Magento\Backend\Block\Widget\Context as WidgetContext;
use Magento\Backend\Model\Auth\StorageInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Integration\Model\Oauth\Token;
use Magento\Security\Model\Config;
use Magento\Store\Model\ScopeInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Block\Adminhtml\Template\AbstractComponent;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando OrderShip Component Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 * @deprecated since 1.0.5 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Order\OrderShip
 */
class Component extends AbstractComponent
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * Component constructor.
     *
     * @param WidgetContext             $context
     * @param WsConfigInterface         $config
     * @param StorageInterface          $session
     * @param AuthenticationInterface   $auth
     * @param Token                     $token
     * @param DateTime                  $dateTime
     * @param RemoteAddress             $remoteAddress
     * @param Config                    $securityConfig
     * @param OrderRepositoryInterface  $orderRepository
     * @param ShipmentProviderInterface $shipmentProvider
     * @param mixed[]                   $data
     */
    public function __construct(
        WidgetContext $context,
        WsConfigInterface $config,
        StorageInterface $session,
        AuthenticationInterface $auth,
        Token $token,
        DateTime $dateTime,
        RemoteAddress $remoteAddress,
        Config $securityConfig,
        OrderRepositoryInterface $orderRepository,
        ShipmentProviderInterface $shipmentProvider,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentProvider = $shipmentProvider;

        parent::__construct(
            $context,
            $config,
            $session,
            $auth,
            $token,
            $dateTime,
            $remoteAddress,
            $securityConfig,
            $data
        );
    }

    /**
     * Obtain order properties for JSON serialization.
     *
     * @return string[]
     */
    private function getOrderProperties()
    {
        return [
            'entity_id',
            'is_virtual',
            'store_id',
            'customer_id',
            'base_shipping_amount',
            'customer_is_guest',
            'billing_address_id',
            'shipping_address_id',
            'weight',
            'total_qty_ordered',
            'base_currency_code',
        ];
    }

    /**
     * Obtain order item properties for JSON serialization.
     *
     * @return string[]
     */
    private function getOrderItemProperties()
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
    private function getOrderAddressProperties()
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
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->shipmentProvider->getSalesShipment();
        $order = $shipment->getOrder();
        return $order;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        $order = $this->getOrder();
        $localeCode = $this->_scopeConfig->getValue(
            DirectoryHelper::XML_PATH_DEFAULT_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $order->getStore()->getCode()
        );

        return strtolower(str_replace('_', '-', $localeCode));
    }

    /**
     * @return null|string
     */
    public function getDefaultCurrency()
    {
        $order = $this->getOrder();
        return $order->getBaseCurrencyCode();
    }

    /**
     * @return string
     */
    public function getDefaultDimensionsUnit()
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
    public function getDefaultWeightUnit()
    {
        $order = $this->getOrder();
        $weightUnit = $this->_scopeConfig->getValue(
            DirectoryHelper::XML_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE,
            $order->getStore()->getCode()
        );

        return $weightUnit;
    }

    /**
     * Obtain Magento REST API endpoint for shipment creation.
     *
     * @return string
     */
    public function getShipEndpoint()
    {
        $orderId = $this->getOrder()->getId();
        $endpoint = $this->_urlBuilder->getDirectUrl("rest/V1/order/$orderId/ship", ['_secure' => true]);

        // core bug workaround, route parameter "_direct" does not get reset
        $this->_urlBuilder->getUrl("rest/V1/order/$orderId/ship", ['_direct' => null]);

        return $endpoint;
    }

    /**
     * Obtain a JSON representation of relevant order data for usage in the
     * OrderShip UI component.
     *
     * @return string
     */
    public function getOrderData()
    {
        $order = $this->getOrder();

        $orderItems = [];
        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getIsVirtual() && !$orderItem->getParentItem()) {
                // skip virtual and child items
                $orderItems[$orderItem->getId()] = $orderItem->toArray($this->getOrderItemProperties());
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

        return json_encode($orderData);
    }

    /**
     * @return string
     */
    public function getSelectedExperience()
    {
        $order = $this->getOrder();
        $shippingMethod = $order->getShippingMethod(true);
        $experienceCode = $shippingMethod->getData('method');

        return $experienceCode;
    }

    /**
     * @return string
     */
    public function getExtOrderId()
    {
        $order = $this->getOrder();

        try {
            $orderReference = $this->orderRepository->getReferenceByOrderId($order->getId());
            $extOrderId = $orderReference->getExtOrderId();
        } catch (LocalizedException $e) {
            $this->_logger->critical($e->getMessage(), ['exception' => $e]);
            $extOrderId = '';
        }

        return $extOrderId;
    }

    /**
     * Obtain shipmentViewPageUrl
     *
     * @return string
     */
    public function getShipmentViewPageUrl()
    {
        return $this->_urlBuilder->getUrl('sales/shipment/view', ['shipment_id' => '--id--']);
    }

    /**
     * @return string
     */
    public function getConfigUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit', [
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
    public function canShow()
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        return (bool) $this->shipmentProvider->getSalesShipment();
    }
}
