<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Rma;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Rma\Api\Data\RmaInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderCollectionPointRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\ViewModel\CoreApiInterface;
use Temando\Shipping\ViewModel\DataProvider\CoreApiAccess;
use Temando\Shipping\ViewModel\DataProvider\CoreApiAccessInterface;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ReturnShipInterface;
use Temando\Shipping\ViewModel\RmaAccessInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * View model for RMA shipment creation.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ReturnShip implements
    ArgumentInterface,
    CoreApiInterface,
    PageActionsInterface,
    ReturnShipInterface,
    RmaAccessInterface,
    ShippingApiInterface
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
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @var OrderCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * ReturnShip constructor.
     *
     * @param CoreApiAccess $coreApiAccess
     * @param ShippingApiAccess $shippingApiAccess
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $serializer
     * @param OrderRepositoryInterface $orderRepository
     * @param RmaAccess $rmaAccess
     * @param OrderCollectionPointRepositoryInterface $collectionPointRepository
     */
    public function __construct(
        CoreApiAccess $coreApiAccess,
        ShippingApiAccess $shippingApiAccess,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig,
        Json $serializer,
        OrderRepositoryInterface $orderRepository,
        RmaAccess $rmaAccess,
        OrderCollectionPointRepositoryInterface $collectionPointRepository
    ) {
        $this->coreApiAccess = $coreApiAccess;
        $this->shippingApiAccess = $shippingApiAccess;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->orderRepository = $orderRepository;
        $this->rmaAccess = $rmaAccess;
        $this->collectionPointRepository = $collectionPointRepository;
    }

    /**
     * Obtain order properties for JSON serialization.
     *
     * @return string[]
     */
    private function getOrderProperties(): array
    {
        return [
            'shipping_address_id',
            'base_currency_code',
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
            'weight',
            'sku',
            'name',
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
     * Obtain array of button data.
     *
     * @see \Temando\Shipping\Block\Adminhtml\ComponentContainer::_prepareLayout
     * @see \Magento\Backend\Block\Widget\Button\ButtonList::add
     *
     * @return mixed[][]
     */
    public function getMainActions(): array
    {
        $buttonId = 'back';
        $buttonUrl = $this->urlBuilder->getUrl('adminhtml/rma/edit', [
            'id' => $this->getRma()->getEntityId(),
        ]);
        $buttonData = [
            'label' => __('Back'),
            'onclick' => sprintf("window.location.href = '%s';", $buttonUrl),
            'class' => 'back',
            'sort_order' => 10
        ];

        $mainActions = [
            $buttonId => $buttonData,
        ];

        return $mainActions;
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
     * @return OrderInterface|\Magento\Sales\Model\Order
     */
    public function getOrder(): OrderInterface
    {
        /** @var \Magento\Rma\Model\Rma $rma */
        $rma = $this->getRma();
        return $rma->getOrder();
    }

    /**
     * @return RmaInterface
     */
    public function getRma(): RmaInterface
    {
        return $this->rmaAccess->getCurrentRma();
    }

    /**
     * @return ShipmentInterface
     */
    public function getRmaShipment(): ShipmentInterface
    {
        return $this->rmaAccess->getCurrentRmaShipment();
    }

    /**
     * @return string
     */
    public function getSaveShipmentIdsEndpoint(): string
    {
        $rmaId = $this->getRma()->getEntityId();
        $endpoint = $this->urlBuilder->getDirectUrl("rest/V1/temando/rma/$rmaId/shipments", ['_secure' => true]);

        // core bug workaround, route parameter "_direct" does not get reset
        $this->urlBuilder->getUrl("rest/V1/temando/rma/$rmaId/shipments", ['_direct' => null]);

        return $endpoint;
    }

    /**
     * @return string
     */
    public function getReturnData(): string
    {
        $rma = $this->getRma();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();

        $rmaItems = $this->rmaAccess->getAuthorizedItems($rma->getEntityId());
        $rmaItemIds = array_keys($rmaItems);

        $returnItems = [];
        foreach ($order->getAllItems() as $orderItem) {
            if (in_array($orderItem->getId(), $rmaItemIds)
                && !$orderItem->getIsVirtual()
                && !$orderItem->getParentItem()
            ) {
                // skip virtual and child items
                $returnItems[$orderItem->getId()] = $orderItem->toArray($this->getOrderItemProperties());
                $returnItems[$orderItem->getId()]['qty_authorized_to_return'] = $rmaItems[$orderItem->getId()]['qty'];
            }
        }

        $orderAddresses = [];
        /** @var \Magento\Sales\Model\Order\Address $orderAddress */
        foreach ($order->getAddresses() as $orderAddress) {
            $orderAddresses[$orderAddress->getId()] = $orderAddress->toArray($this->getOrderAddressProperties());
            $orderAddresses[$orderAddress->getId()]['region'] = $orderAddress->getRegionCode();
        }

        $returnData = $order->toArray($this->getOrderProperties());
        $returnData['addresses'] = $orderAddresses;
        $returnData['items'] = $returnItems;

        try {
            $collectionPoint = $this->collectionPointRepository->get($returnData['shipping_address_id']);
            $returnData['final_recipient_address_id'] = $returnData['shipping_address_id'];
            $returnData['shipping_address_id'] = $collectionPoint->getCollectionPointId();
            $returnData['addresses'][$collectionPoint->getCollectionPointId()] = [
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
            $returnData['final_recipient_address_id'] = $returnData['shipping_address_id'];
        }

        return $this->serializer->serialize($returnData);
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
    public function getExtOrderId(): string
    {
        try {
            /** @var \Temando\Shipping\Api\Data\Order\OrderReferenceInterface $orderReference */
            $orderReference = $this->orderRepository->getReferenceByOrderId($this->getRma()->getOrderId());

            return $orderReference->getExtOrderId();
        } catch (NoSuchEntityException $noSuchEntityException) {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getRmaShipmentPageUrl(): string
    {
        return $this->urlBuilder->getUrl('temando/rma_shipment/view', [
            'rma_id' => $this->getRma()->getEntityId(),
            'ext_shipment_id' => '--id--',
        ]);
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
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();
        $weightUnit = $this->scopeConfig->getValue(
            DirectoryHelper::XML_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE,
            $order->getStore()->getCode()
        );

        return $weightUnit;
    }
}
