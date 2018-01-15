<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Block\Adminhtml\Shipping\Create;

use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment as SalesShipment;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Sales\Api\OrderRepositoryInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Test\Integration\Fixture\PlacedOrderFixture;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Temando Create Shipment Page OrderShip component Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CreateShipmentComponentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * delegate fixtures creation to separate class.
     */
    public static function createQuoteAndOrderFixture()
    {
        PlacedOrderFixture::createQuoteAndOrderFixture();
    }

    /**
     * delegate fixtures creation to separate class.
     */
    public static function createOrderReferenceFixture()
    {
        PlacedOrderFixture::createOrderReferenceFixture();
    }

    /**
     * delegate fixtures rollback to separate class.
     */
    public static function createQuoteAndOrderFixtureRollback()
    {
        PlacedOrderFixture::createQuoteAndOrderFixtureRollback();
    }

    /**
     * delegate fixtures rollback to separate class.
     */
    public static function createOrderReferenceFixtureRollback()
    {
        PlacedOrderFixture::createOrderReferenceFixtureRollback();
    }

    protected function tearDown()
    {
        Bootstrap::getObjectManager()->removeSharedInstance(Component::class);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function shipmentComponentHasNoBackUrl()
    {
        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class);

        $this->assertEmpty($block->getBackUrl());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture foo_store general/locale/code en_GB
     */
    public function getLocaleCode()
    {
        $order = new DataObject([
            'store' => new DataObject([
                'code' => 'foo',
            ]),
        ]);
        /** @var SalesShipment|\PHPUnit_Framework_MockObject_MockObject $shipmentMock */
        $shipmentMock = $this->getMockBuilder(SalesShipment::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class, '', [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals('en-gb', $block->getLocale());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function getDefaultCurrency()
    {
        $currencyCode = 'XXX';
        $order = new DataObject([
            'base_currency_code' => $currencyCode,
        ]);
        /** @var SalesShipment|\PHPUnit_Framework_MockObject_MockObject $shipmentMock */
        $shipmentMock = $this->getMockBuilder(SalesShipment::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class, '', [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals($currencyCode, $block->getDefaultCurrency());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture foo_store general/locale/weight_unit BAR
     */
    public function getDefaultWeightUnit()
    {
        $order = new DataObject([
            'store' => new DataObject([
                'code' => 'foo',
            ]),
        ]);
        /** @var SalesShipment|\PHPUnit_Framework_MockObject_MockObject $shipmentMock */
        $shipmentMock = $this->getMockBuilder(SalesShipment::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class, '', [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals('BAR', $block->getDefaultWeightUnit());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture foo_store general/locale/weight_unit lbs
     */
    public function getDimensionsUnitForLbsWeight()
    {
        $order = new DataObject([
            'store' => new DataObject([
                'code' => 'foo',
            ]),
        ]);
        /** @var SalesShipment|\PHPUnit_Framework_MockObject_MockObject $shipmentMock */
        $shipmentMock = $this->getMockBuilder(SalesShipment::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class, '', [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals('in', $block->getDefaultDimensionsUnit());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture foo_store general/locale/weight_unit kg
     */
    public function getDimensionsUnitForKgWeight()
    {
        $order = new DataObject([
            'store' => new DataObject([
                'code' => 'foo',
            ]),
        ]);
        /** @var SalesShipment|\PHPUnit_Framework_MockObject_MockObject $shipmentMock */
        $shipmentMock = $this->getMockBuilder(SalesShipment::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class, '', [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals('cm', $block->getDefaultDimensionsUnit());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function getShipEndpoint()
    {
        $orderId = '808';
        $order = new DataObject([
            'id' => $orderId,
        ]);
        /** @var SalesShipment|\PHPUnit_Framework_MockObject_MockObject $shipmentMock */
        $shipmentMock = $this->getMockBuilder(SalesShipment::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class, '', [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertStringEndsWith("$orderId/ship", $block->getShipEndpoint());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoDataFixture createQuoteAndOrderFixture
     */
    public function getOrderData()
    {
        $orderIncrementId = PlacedOrderFixture::getOrderIncrementId();

        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $orderIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $searchResult */
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepositoryInterface::class);
        $searchResult = $orderRepository->getList($searchCriteriaBuilder->create());
        /** @var \Magento\Sales\Model\Order $order */
        $order = $searchResult->getFirstItem();

        /** @var SalesShipment|\PHPUnit_Framework_MockObject_MockObject $shipmentMock */
        $shipmentMock = $this->getMockBuilder(SalesShipment::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class, '', [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertJson($block->getOrderData());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function getSelectedExperience()
    {
        $methodCode = 'uk-standard-hermes';
        $order = Bootstrap::getObjectManager()->create(Order::class, ['data' => [
            'shipping_method' => "temando_{$methodCode}",
        ]]);

        /** @var SalesShipment|\PHPUnit_Framework_MockObject_MockObject $shipmentMock */
        $shipmentMock = $this->getMockBuilder(SalesShipment::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class, '', [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals($methodCode, $block->getSelectedExperience());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoDataFixture createOrderReferenceFixture
     */
    public function getExtOrderId()
    {
        $orderIncrementId = PlacedOrderFixture::getOrderIncrementId();
        $extOrderId = PlacedOrderFixture::getExternalOrderId();

        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $orderIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $searchResult */
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepositoryInterface::class);
        $searchResult = $orderRepository->getList($searchCriteriaBuilder->create());
        /** @var \Magento\Sales\Model\Order $order */
        $order = $searchResult->getFirstItem();

        /** @var SalesShipment|\PHPUnit_Framework_MockObject_MockObject $shipmentMock */
        $shipmentMock = $this->getMockBuilder(SalesShipment::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class, '', [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals($extOrderId, $block->getExtOrderId());
    }

    /**
     * Assert shipment view url template contains {id} placeholder.
     *
     * @test
     * @magentoAppArea adminhtml
     */
    public function getShipmentViewPageUrl()
    {
        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Component $block */
        $block = $layout->createBlock(Component::class);

        $this->assertContains('sales/shipment/view', $block->getShipmentViewPageUrl());
        $this->assertContains('shipment_id', $block->getShipmentViewPageUrl());
    }
}
