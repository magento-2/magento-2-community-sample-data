<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Test\Integration\Fixture;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\Address as OrderAddress;
use Magento\Sales\Model\OrderRepository;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Model\QuoteRepository;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;
use Temando\Shipping\Model\Order\OrderReference;
use Temando\Shipping\Model\ResourceModel\Order\OrderRepository as OrderReferenceRepository;

/**
 * PlacedOrderFixture
 *
 * @package  Temando\Shipping\Test
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
final class PlacedOrderFixture
{
    /**
     * @var string
     */
    private static $orderIncrementId = '0000000303';

    /**
     * @var string
     */
    private static $externalOrderId = '00000000-3000-0003-0000-000000000000';

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
            'unit_price' => 38,
        ],
        'FX-24-WG03' => [
            'name' => 'Clamber Fixture Watch',
            'qty' => 2,
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
     * @param string $addressType
     * @return QuoteAddress
     */
    private function getQuoteAddress($addressType)
    {
        $address = Bootstrap::getObjectManager()->create(QuoteAddress::class, ['data' => [
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
        $shippingMethod = 'temando_standard';
        $shippingCost = 7.95;
        $subTotal = 0;

        /** @var \Magento\Sales\Model\Order $order */
        $order = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order::class, ['data' => [
            'increment_id' => $orderIncrementId,
            'quote_id' => $quoteId,
            'store_id' => $storeId,
            'created_at' => $orderDate,
            'updated_at' => $orderDate,
            'shipping_method' => $shippingMethod,
            'shipping_amount' => $shippingCost,
            'base_shipping_amount' => $shippingCost,
            'base_currency_code' => $currency,
            'store_currency_code' => $currency,
            'customer_email' => $this->customerData['email'],
            'customer_firstname' => $this->customerData['firstname'],
            'customer_lastname' => $this->customerData['lastname'],
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
                'qty_shipped' => 0,
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
     * @param string $orderIncrementId
     * @param string $storeId
     * @param string $currency
     * @return \Magento\Quote\Model\Quote
     */
    private function getQuote($orderIncrementId, $storeId = '1', $currency = 'USD')
    {
        $quoteDate = date('Y-m-d H:i:s');
        $shippingCost = 7.95;
        $subTotal = 0;

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = Bootstrap::getObjectManager()->create(\Magento\Quote\Model\Quote::class, ['data' => [
            'store_id' => $storeId,
            'created_at' => $quoteDate,
            'updated_at' => $quoteDate,
            'base_currency_code' => $currency,
            'store_currency_code' => $currency,
            'customer_email' => $this->customerData['email'],
            'customer_firstname' => $this->customerData['firstname'],
            'customer_lastname' => $this->customerData['lastname'],
            'reserved_order_id' => $orderIncrementId,
            'is_active' => '0', // already converted to order
        ]]);

        foreach ($this->productData as $sku => $productData) {
            $quoteItemQty = $productData['qty'];
            $quoteItemUnitPrice = $productData['unit_price'];
            $quoteItem = Bootstrap::getObjectManager()->create(\Magento\Quote\Model\Quote\Item::class, ['data' => [
                'created_at' => $quoteDate,
                'store_id' => $storeId,
                'is_virtual' => false,
                'sku' => $sku,
                'name' => $productData['name'],
                'qty' => $quoteItemQty,
                'price' => $quoteItemUnitPrice,
                'base_price' => $quoteItemUnitPrice,
                'row_total' => $quoteItemQty * $quoteItemUnitPrice,
                'base_row_total' => $quoteItemQty * $quoteItemUnitPrice,
                'product_type' => 'simple',
                'price_incl_tax' => $quoteItemUnitPrice,
                'base_price_incl_tax' => $quoteItemUnitPrice,
                'row_total_incl_tax' => $quoteItemQty * $quoteItemUnitPrice,
                'base_row_total_incl_tax' => $quoteItemQty * $quoteItemUnitPrice,
                'product_id' => $productData['entity_id'],
            ]]);
            $quote->addItem($quoteItem);
            $subTotal += ($quoteItemQty * $quoteItemUnitPrice);
        }

        $quote->setItemsCount(count($quote->getItems()));
        $quote->setSubtotal($subTotal);
        $quote->setBaseSubtotal($subTotal);
        $quote->setGrandTotal($subTotal + $shippingCost);
        $quote->setBaseGrandTotal($subTotal + $shippingCost);

        return $quote;
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
    public static function getExternalOrderId()
    {
        return self::$externalOrderId;
    }

    // ------------------------- PUBLIC ENTRYPOINTS ------------------------- //

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order
     */
    public function createQuoteAndOrder()
    {
        $orderIncrementId = $this->getOrderIncrementId();

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

        // save quote
        $cartRepository = Bootstrap::getObjectManager()->get(QuoteRepository::class);
        $quote = $this->getQuote($orderIncrementId);
        $quoteBillingAddress = $this->getQuoteAddress(QuoteAddress::ADDRESS_TYPE_BILLING);
        $quoteShippingAddress = $this->getQuoteAddress(QuoteAddress::ADDRESS_TYPE_SHIPPING);

        $quote->setBillingAddress($quoteBillingAddress);
        $quote->setShippingAddress($quoteShippingAddress);
        $cartRepository->save($quote);

        // save order
        /** @var OrderRepository $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepository::class);
        $order = $this->getOrder($orderIncrementId, $quote->getId());
        $orderBillingAddress = $this->getOrderAddress(OrderAddress::TYPE_BILLING);
        $orderShippingAddress = $this->getOrderAddress(OrderAddress::TYPE_SHIPPING);
        $payment = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order\Payment::class, ['data' => [
            'method' => 'checkmo',
        ]]);

        $order->setBillingAddress($orderBillingAddress);
        $order->setShippingAddress($orderShippingAddress);
        $order->setPayment($payment);

        return $orderRepository->save($order);
    }

    public function createOrderReference()
    {
        $order = $this->createQuoteAndOrder();

        // save external order reference
        /** @var OrderReferenceRepository $orderReferenceRepository */
        $orderReferenceRepository = Bootstrap::getObjectManager()->get(OrderReferenceRepository::class);
        $orderReference = Bootstrap::getObjectManager()->create(OrderReference::class, ['data' => [
            OrderReferenceInterface::ORDER_ID => $order->getId(),
            OrderReferenceInterface::EXT_ORDER_ID => self::$externalOrderId,
        ]]);
        $orderReferenceRepository->saveReference($orderReference);
    }

    public function rollbackQuoteAndOrder()
    {
        $orderIncrementId = $this->getOrderIncrementId();

        /** @var OrderRepository $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepository::class);
        /** @var QuoteRepository $cartRepository */
        $cartRepository = Bootstrap::getObjectManager()->get(QuoteRepository::class);
        /** @var ProductRepository $productRepository */
        $productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $orderIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        $searchResult = $orderRepository->getList($searchCriteriaBuilder->create());
        foreach ($searchResult as $order) {
            $orderRepository->delete($order);
        }

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('reserved_order_id', $orderIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        $searchResult = $cartRepository->getList($searchCriteriaBuilder->create());
        foreach ($searchResult->getItems() as $quote) {
            $cartRepository->delete($quote);
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
     * - quote with quote items
     * - order with order items
     */
    public static function createQuoteAndOrderFixture()
    {
        /** @var PlacedOrderFixture $self */
        $self = Bootstrap::getObjectManager()->create(static::class);
        $self->createQuoteAndOrder();
    }

    /**
     * Create fixtures:
     * - products
     * - quote with quote items
     * - order with order items
     * - temando external order reference
     */
    public static function createOrderReferenceFixture()
    {
        /** @var PlacedOrderFixture $self */
        $self = Bootstrap::getObjectManager()->create(static::class);
        $self->createOrderReference();
    }

    /**
     * Rollback fixtures:
     * - order
     * - quote
     * - products
     */
    public static function createQuoteAndOrderFixtureRollback()
    {
        /** @var PlacedOrderFixture $self */
        $self = Bootstrap::getObjectManager()->create(static::class);
        $self->rollbackQuoteAndOrder();
    }

    /**
     * Rollback fixtures:
     * - order
     * - quote
     * - products
     */
    public static function createOrderReferenceFixtureRollback()
    {
        /** @var PlacedOrderFixture $self */
        $self = Bootstrap::getObjectManager()->create(static::class);
        $self->rollbackQuoteAndOrder();
    }
}
