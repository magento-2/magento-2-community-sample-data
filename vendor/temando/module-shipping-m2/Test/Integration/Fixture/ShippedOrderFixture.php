<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Test\Integration\Fixture;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Address as OrderAddress;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\OrderRepository;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Model\ResourceModel\Shipment\ShipmentReferenceRepository;
use Temando\Shipping\Model\Shipment\ShipmentReference;

/**
 * ShippedOrderFixture
 *
 * @package  Temando\Shipping\Test
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
final class ShippedOrderFixture
{
    /**
     * @var string
     */
    private static $orderIncrementId = '0000000303';

    /**
     * @var string
     */
    private static $shipmentIncrementId = '0000000505';

    /**
     * @var string[]
     */
    private static $shipmentReferenceData = [
        'id' => '00000000-5000-0005-0000-000000000000',
        'location' => '00000000-6000-0006-0000-000000000000',
        'tracking' => '00000000-7000-0007-0000-000000000000',
        'tracking_url' => 'https://example.org/foo.pdf',
    ];

    /**
     * @var string[]
     */
    private $customerData = [
        'email' => 'foo@example.org',
        'firstname' => 'Foo',
        'lastname' => 'Example',
        'company' => 'Temando',
        'street' => '550 Montgomery St.',
        'city' => 'California',
        'postcode' => '94111',
        'country' => 'US',
        'phone' => '911',
    ];

    /**
     * @var string[]
     */
    private $productData = [
        'FX-24-MB03' => [
            'name' => 'Crown Summit Fixture Backpack',
            'qty' => 1,
            'weight' => 0.95,
            'unit_price' => 38,
        ],
        'FX-24-WG03' => [
            'name' => 'Clamber Fixture Watch',
            'qty' => 2,
            'weight' => 0.43,
            'unit_price' => 54,
        ],
    ];

    /**
     * @param string $addressType
     * @return OrderAddress
     */
    private function getOrderAddress($addressType)
    {
        $address = Bootstrap::getObjectManager()->create(OrderAddress::class, ['data' => [
            'address_type' => $addressType,
            'email' => $this->customerData['email'],
            'firstname' => $this->customerData['firstname'],
            'lastname' => $this->customerData['lastname'],
            'company' => $this->customerData['company'],
            'street' => $this->customerData['street'],
            'city' => $this->customerData['city'],
            'postcode' => $this->customerData['postcode'],
            'country_id' => $this->customerData['country'],
            'telephone' => $this->customerData['phone'],
        ]]);

        return $address;
    }

    /**
     * @param string $orderIncrementId
     * @param string $quoteId
     * @param string $storeId
     * @param string $currency
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder($orderIncrementId, $quoteId, $storeId = '1', $currency = 'USD')
    {
        $orderDate = date('Y-m-d H:i:s');
        $shippingCost = 7.95;
        $subTotal = 0;

        /** @var \Magento\Sales\Model\Order $order */
        $order = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order::class, ['data' => [
            'increment_id' => $orderIncrementId,
            'quote_id' => $quoteId,
            'store_id' => $storeId,
            'created_at' => $orderDate,
            'updated_at' => $orderDate,
            'shipping_amount' => $shippingCost,
            'base_shipping_amount' => $shippingCost,
            'base_currency_code' => $currency,
            'store_currency_code' => $currency,
            'customer_email' => $this->customerData['email'],
            'customer_firstname' => $this->customerData['firstname'],
            'customer_lastname' => $this->customerData['lastname'],
            'shipping_method' => \Temando\Shipping\Model\Shipping\Carrier::CODE . '_foo',
        ]]);

        foreach ($this->productData as $sku => $productData) {
            $orderItemQty = $productData['qty'];
            $orderItemUnitPrice = $productData['unit_price'];
            $orderItem = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order\Item::class, ['data' => [
                'created_at' => $orderDate,
                'store_id' => $storeId,
                'is_virtual' => false,
                'sku' => $sku,
                'name' => $productData['name'],
                'product_id' => $productData['entity_id'],
                'qty' => $orderItemQty,
                'price' => $orderItemUnitPrice,
                'base_price' => $orderItemUnitPrice,
                'row_total' => $orderItemQty * $orderItemUnitPrice,
                'base_row_total' => $orderItemQty * $orderItemUnitPrice,
                'product_type' => 'simple',
                'price_incl_tax' => $orderItemUnitPrice,
                'base_price_incl_tax' => $orderItemUnitPrice,
                'row_total_incl_tax' => $orderItemQty * $orderItemUnitPrice,
                'base_row_total_incl_tax' => $orderItemQty * $orderItemUnitPrice,
                'qty_ordered' => $productData['qty'],
                'qty_shipped' => $productData['qty'],
                'qty_refunded' => 0,
                'qty_canceled' => 0,
            ]]);
            $order->addItem($orderItem);
            $subTotal += ($orderItemQty * $orderItemUnitPrice);
        }

        $order->setTotalItemCount(count($order->getItems()));
        $order->setSubtotal($subTotal);
        $order->setBaseSubtotal($subTotal);
        $order->setGrandTotal($subTotal + $shippingCost);
        $order->setBaseGrandTotal($subTotal + $shippingCost);

        return $order;
    }

    /**
     * @param string $shipmentIncrementId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return ShipmentInterface
     */
    private function getShipment($shipmentIncrementId, $order)
    {
        $shipmentDate = date('Y-m-d H:i:s');

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = Bootstrap::getObjectManager()->create(ShipmentInterface::class, ['data' => [
            'increment_id' => $shipmentIncrementId,
            'order_id' => $order->getEntityId(),
            'store_id' => $order->getStoreId(),
            'created_at' => $shipmentDate,
            'updated_at' => $shipmentDate,
            'total_weight' => null,
            'total_qty' => $order->getTotalItemCount(),
            'email_sent' => 1,
            'send_email' => 1,
        ]]);

        foreach ($order->getItems() as $orderItem) {
            $shipmentItem = Bootstrap::getObjectManager()->create(
                \Magento\Sales\Model\Order\Shipment\Item::class,
                ['data' => [
                    'price' => $orderItem->getPrice(),
                    'weight' => $orderItem->getWeight(),
                    'qty' => $orderItem->getQtyOrdered(),
                    'product_id' => $orderItem->getProductId(),
                    'order_item_id' => $orderItem->getItemId(),
                    'name' => $orderItem->getName(),
                    'sku' => $orderItem->getSku(),
                ]]
            );
            $shipment->addItem($shipmentItem);
        }

        return $shipment;
    }

    // ---------- GETTERS FOR ASSERTIONS / LOADING FIXTURE ENTITIES --------- //

    /**
     * @return string
     */
    public static function getOrderIncrementId()
    {
        return self::$orderIncrementId;
    }

    /**
     * @return string
     */
    public static function getShipmentIncrementId()
    {
        return self::$shipmentIncrementId;
    }

    /**
     * @return string[]
     */
    public static function getShipmentReferenceData()
    {
        return self::$shipmentReferenceData;
    }

    // ------------------------- PUBLIC ENTRYPOINTS ------------------------- //

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function createOrderAndShipment()
    {
        $orderIncrementId = $this->getOrderIncrementId();
        $shipmentIncrementId = $this->getShipmentIncrementId();

        // save products
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
        foreach ($this->productData as $sku => &$productData) {
            $product = Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class, ['data' => [
                'attribute_set_id' => '4',
                'type_id' => 'simple',
                'sku' => $sku,
                'name' => $productData['name'],
                'price' => $productData['unit_price'],
            ]]);
            $product = $productRepository->save($product);
            $productData['entity_id'] = $product->getId();
        }

        // save order
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepository::class);
        $order = $this->getOrder($orderIncrementId, null);
        $orderBillingAddress = $this->getOrderAddress(OrderAddress::TYPE_BILLING);
        $orderShippingAddress = $this->getOrderAddress(OrderAddress::TYPE_SHIPPING);
        $payment = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order\Payment::class, ['data' => [
            'method' => 'checkmo',
        ]]);

        $order->setBillingAddress($orderBillingAddress);
        $order->setShippingAddress($orderShippingAddress);
        $order->setPayment($payment);

        $orderRepository->save($order);

        // save shipment
        $shipmentRepository = Bootstrap::getObjectManager()->get(ShipmentRepository::class);
        $shipment = $this->getShipment($shipmentIncrementId, $order);
        $shipment->setBillingAddressId($orderBillingAddress->getId());
        $shipment->setShippingAddressId($orderShippingAddress->getId());

        $shipmentRepository->save($shipment);

        // save external shipment reference
        /** @var ShipmentReferenceRepository $shipmentReferenceRepository */
        $shipmentReferenceRepository = Bootstrap::getObjectManager()->get(ShipmentReferenceRepository::class);
        $shipmentReference = Bootstrap::getObjectManager()->create(ShipmentReference::class, ['data' => [
            ShipmentReferenceInterface::SHIPMENT_ID => $shipment->getEntityId(),
            ShipmentReferenceInterface::EXT_SHIPMENT_ID => self::$shipmentReferenceData['id'],
            ShipmentReferenceInterface::EXT_LOCATION_ID => self::$shipmentReferenceData['location'],
            ShipmentReferenceInterface::EXT_TRACKING_REFERENCE => self::$shipmentReferenceData['tracking'],
            ShipmentReferenceInterface::EXT_TRACKING_URL => self::$shipmentReferenceData['tracking_url'],
        ]]);

        $shipmentReferenceRepository->save($shipmentReference);

        return $order;
    }

    public function rollbackOrderAndShipment()
    {
        $orderIncrementId = $this->getOrderIncrementId();

        /** @var OrderRepository $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepository::class);
        /** @var ProductRepositoryInterface|ProductRepository $productRepository */
        $productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $orderIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        $searchResult = $orderRepository->getList($searchCriteriaBuilder->create());
        foreach ($searchResult as $order) {
            $orderRepository->delete($order);
        }

        $skus = array_keys($this->productData);
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('sku', $skus, 'in');
        $searchResult = $productRepository->getList($searchCriteriaBuilder->create());
        foreach ($searchResult->getItems() as $product) {
            $productRepository->delete($product);
        }

        $productRepository->cleanCache();
    }

    // ------------------------- STATIC ENTRYPOINTS ------------------------- //

    /**
     * Create fixtures:
     * - products
     * - order with order items
     * - shipment with shipment items
     */
    public static function createOrderAndShipmentFixture()
    {
        /** @var ShippedOrderFixture $self */
        $self = Bootstrap::getObjectManager()->create(static::class);
        $self->createOrderAndShipment();
    }

    /**
     * Rollback fixtures:
     * - order (incl. shipment and ext shipment reference)
     * - products
     */
    public static function createOrderAndShipmentFixtureRollback()
    {
        /** @var ShippedOrderFixture $self */
        $self = Bootstrap::getObjectManager()->create(static::class);
        $self->rollbackOrderAndShipment();
    }
}
