<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\ViewModel\Order;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Integration\Model\Oauth\Token;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment as SalesShipment;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use Temando\Shipping\Model\Shipment\ShipmentProvider;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Test\Connection\Db\TokenResourceFake;
use Temando\Shipping\Test\Integration\Fixture\PlacedOrderFixture;
use Temando\Shipping\ViewModel\CoreApiInterface;
use Temando\Shipping\ViewModel\DataProvider\CoreApiAccess;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * Temando Order Ship View Model Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderShipViewTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown()
    {
        Bootstrap::getObjectManager()->removeSharedInstance(ShipmentProvider::class);

        parent::tearDown();
    }

    /**
     * @param string $storeCode
     * @return OrderInterface
     */
    private function getOrder(string $storeCode = 'foo'): OrderInterface
    {
        $frontEndStore = $this->getMockBuilder(Store::class)
            ->setMethods(['getCode'])
            ->disableOriginalConstructor()
            ->getMock();
        $frontEndStore
            ->expects($this->any())
            ->method('getCode')
            ->willReturn($storeCode);

        $storeManager = $this->getMockBuilder(StoreManager::class)
            ->setMethods(['getStore'])
            ->disableOriginalConstructor()
            ->getMock();
        $storeManager
            ->expects($this->any())
            ->method('getStore')
            ->willReturn($frontEndStore);

        /** @var Order $order */
        $order = Bootstrap::getObjectManager()->create(Order::class, [
            'storeManager' => $storeManager,
        ]);

        return $order;
    }

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

    /**
     * @test
     */
    public function backButtonIsNotAvailableInOrderShipComponent()
    {
        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->get(OrderShip::class);
        $this->assertNotInstanceOf(PageActionsInterface::class, $viewModel);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     */
    public function shippingApiCredentialsAreAvailableInOrderShipComponent()
    {
        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->get(OrderShip::class);
        $this->assertInstanceOf(ShippingApiInterface::class, $viewModel);
        $this->assertEquals('https://foo.temando.io/v1/', $viewModel->getShippingApiAccess()->getApiEndpoint());
    }

    /**
     * @test
     * @magentoConfigFixture default/admin/security/session_lifetime 303
     */
    public function coreApiCredentialsAreAvailableInOrderShipComponent()
    {
        $currentTime = time();
        $sessionExpirationTime = $currentTime + 303;

        $adminId = 77;
        $adminUser = Bootstrap::getObjectManager()->create(User::class, ['data' => [
            'user_id' => $adminId,
        ]]);

        $session = $this->getMockBuilder(Session::class)
            ->setMethods(['getUser', 'getUpdatedAt'])
            ->disableOriginalConstructor()
            ->getMock();
        $session
            ->expects($this->any())
            ->method('getUser')
            ->willReturn($adminUser);
        $session
            ->expects($this->any())
            ->method('getUpdatedAt')
            ->willReturn($currentTime);

        $resource = Bootstrap::getObjectManager()->create(TokenResourceFake::class);
        $token = Bootstrap::getObjectManager()->create(Token::class, [
            'resource' => $resource,
        ]);

        $apiAccess = Bootstrap::getObjectManager()->create(CoreApiAccess::class, [
            'session' => $session,
            'token' => $token,
        ]);

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'coreApiAccess' => $apiAccess,
        ]);

        $this->assertInstanceOf(CoreApiInterface::class, $viewModel);
        $this->assertEquals($sessionExpirationTime, $viewModel->getCoreApiAccess()->getSessionExpirationTime());
        $this->assertNotEmpty($viewModel->getCoreApiAccess()->getAccessToken());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function getDefaultCurrency()
    {
        $currencyCode = 'XXX';
        $order = $this->getOrder();
        $order->setBaseCurrencyCode($currencyCode);

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

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals($currencyCode, $viewModel->getDefaultCurrency());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture foo_store general/locale/weight_unit BAR
     */
    public function getDefaultWeightUnit()
    {
        $order = $this->getOrder();

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

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals('BAR', $viewModel->getDefaultWeightUnit());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture foo_store general/locale/weight_unit lbs
     */
    public function getDimensionsUnitForLbsWeight()
    {
        $order = $this->getOrder();

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

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals('in', $viewModel->getDefaultDimensionsUnit());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture foo_store general/locale/weight_unit kg
     */
    public function getDimensionsUnitForKgWeight()
    {
        $order = $this->getOrder();

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

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals('cm', $viewModel->getDefaultDimensionsUnit());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function getShipEndpoint()
    {
        $orderId = '808';
        $order = $this->getOrder();
        $order->setEntityId($orderId);

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

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertStringEndsWith("$orderId/ship", $viewModel->getShipEndpoint());
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
            ->expects($this->any())
            ->method('getOrder')
            ->willReturn($order);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipmentMock);

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertJson($viewModel->getOrderData());
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

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals($methodCode, $viewModel->getSelectedExperience());
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

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals($extOrderId, $viewModel->getExtOrderId());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function noExtOrderIdFound()
    {
        $orderId = '808';
        $order = $this->getOrder();
        $order->setEntityId($orderId);

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

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertEquals('', $viewModel->getExtOrderId());
    }

    /**
     * Assert shipment view url template contains id placeholder.
     *
     * @test
     * @magentoAppArea adminhtml
     */
    public function getShipmentViewPageUrl()
    {
        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class);

        $url = $viewModel->getShipmentViewPageUrl();
        $this->assertContains('sales/shipment/view', $url);
        $this->assertContains('shipment_id', $url);
        $this->assertContains('--id--', $url);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function getConfigUrl()
    {
        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class);

        $url = $viewModel->getConfigUrl();
        $this->assertContains('system_config/edit', $url);
        $this->assertContains('carriers', $url);
        $this->assertContains('#carriers_temando-link', $url);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function shipmentWasRegistered()
    {
        /** @var SalesShipment $shipment */
        $shipment = Bootstrap::getObjectManager()->create(SalesShipment::class);

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProviderInterface::class);
        $shipmentProvider->setSalesShipment($shipment);

        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class, [
            'shipmentProvider' => $shipmentProvider,
        ]);

        $this->assertTrue($viewModel->hasSalesShipment());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function shipmentWasNotRegistered()
    {
        /** @var OrderShip $viewModel */
        $viewModel = Bootstrap::getObjectManager()->create(OrderShip::class);

        $this->assertFalse($viewModel->hasSalesShipment());
    }
}
