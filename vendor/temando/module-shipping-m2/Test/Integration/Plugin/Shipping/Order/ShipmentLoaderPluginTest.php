<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Shipping;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\TestFramework\ObjectManager;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Plugin\Shipping\Order\ShipmentLoaderPlugin;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Rest\RestClient;
use Temando\Shipping\Test\Integration\Fixture\ShippedOrderFixture;
use Temando\Shipping\Test\Integration\Provider\RestResponseProvider;
use Temando\Shipping\Webservice\Exception\HttpResponseException;

/**
 * ShipmentLoaderPluginTest
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @magentoAppIsolation enabled
 */
class ShipmentLoaderPluginTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var RestClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $restClient;

    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var ShipmentLoader
     */
    private $shipmentLoader;

    /**
     * @var RequestInterface|\Magento\TestFramework\Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * delegate fixtures creation to separate class.
     */
    public static function createOrderAndShipmentFixture()
    {
        ShippedOrderFixture::createOrderAndShipmentFixture();
    }

    /**
     * delegate fixtures rollback to separate class.
     */
    public static function createOrderAndShipmentFixtureRollback()
    {
        ShippedOrderFixture::createOrderAndShipmentFixtureRollback();
    }

    /**
     * @return string[]
     */
    public function getShipmentResponseDataProvider()
    {
        return RestResponseProvider::getShipmentResponseDataProvider();
    }

    /**
     * Init object manager
     */
    protected function setUp()
    {
        parent::setUp();

        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->setData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY, '2038-01-19T03:03:33.000Z');

        $this->objectManager = Bootstrap::getObjectManager();

        $this->restClient = $this->getMockBuilder(RestClient::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager->addSharedInstance($this->restClient, RestClient::class);

        $this->request = $this->getMockBuilder(\Magento\TestFramework\Request::class)
            ->setMethods(['getModuleName', 'getControllerName', 'getActionName'])
            ->disableOriginalConstructor()
            ->getMock();
        $plugin = $this->objectManager->create(ShipmentLoaderPlugin::class, [
            'request' => $this->request,
        ]);

        $this->objectManager->addSharedInstance($plugin, ShipmentLoaderPlugin::class);

        $this->shipmentRepository = $this->objectManager->create(ShipmentRepository::class);
        $this->shipmentLoader = $this->objectManager->create(ShipmentLoader::class);
    }

    protected function tearDown()
    {
        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->unsetData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY);

        parent::tearDown();
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function platformShipmentIsNotLoadedIfNoShipmentIsRegistered()
    {
        $this->request->expects($this->never())->method('getControllerName');
        $this->request->expects($this->never())->method('getActionName');

        $this->shipmentLoader->load();

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = $this->objectManager->get(ShipmentProviderInterface::class);
        $platformShipment = $shipmentProvider->getShipment();
        $this->assertNull($platformShipment);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoDataFixture createOrderAndShipmentFixture
     */
    public function platformShipmentIsNotLoadedIfNotOnShipmentViewPage()
    {
        $shipmentIncrementId = ShippedOrderFixture::getShipmentIncrementId();

        $this->request->expects($this->once())->method('getControllerName')->willReturn('catalog_product');
        $this->request->expects($this->once())->method('getActionName')->willReturn('edit');

        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $shipmentIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        $searchResult = $this->shipmentRepository->getList($searchCriteriaBuilder->create());

        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $searchResult */
        $this->assertInstanceOf(\Magento\Sales\Model\ResourceModel\Order\Shipment\Collection::class, $searchResult);
        $this->assertCount(1, $searchResult);
        $this->assertContainsOnlyInstancesOf(\Magento\Sales\Model\Order\Shipment::class, $searchResult);

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $searchResult->getFirstItem();
        $this->shipmentLoader
            ->setData('shipment_id', $shipment->getId())
            ->load();

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = $this->objectManager->get(ShipmentProviderInterface::class);
        $platformShipment = $shipmentProvider->getShipment();
        $this->assertNull($platformShipment);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoDataFixture createOrderAndShipmentFixture
     */
    public function platformShipmentIsNotLoadedWithDefaultCarrier()
    {
        $shipmentIncrementId = ShippedOrderFixture::getShipmentIncrementId();

        $this->request->expects($this->once())->method('getControllerName')->willReturn('order_shipment');
        $this->request->expects($this->once())->method('getActionName')->willReturn('view');

        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $shipmentIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        $searchResult = $this->shipmentRepository->getList($searchCriteriaBuilder->create());

        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $searchResult */
        $this->assertInstanceOf(\Magento\Sales\Model\ResourceModel\Order\Shipment\Collection::class, $searchResult);
        $this->assertCount(1, $searchResult);
        $this->assertContainsOnlyInstancesOf(\Magento\Sales\Model\Order\Shipment::class, $searchResult);

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $searchResult->getFirstItem();
        $shipment->getOrder()->setShippingMethod('flatrate_flatrate')->save();
        $this->shipmentLoader
            ->setData('shipment_id', $shipment->getId())
            ->load();

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = $this->objectManager->get(ShipmentProviderInterface::class);
        $platformShipment = $shipmentProvider->getShipment();
        $this->assertNull($platformShipment);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoDataFixture createOrderAndShipmentFixture
     */
    public function platformShipmentLoadingThrowsApiException()
    {
        $shipmentIncrementId = ShippedOrderFixture::getShipmentIncrementId();

        $eCode = '700';
        $eMsg = 'bar';
        $jsonResponse = sprintf('{"errors": [{"code": "%s", "title": "%s"}]}', $eCode, $eMsg);

        $this->request->expects($this->once())->method('getControllerName')->willReturn('order_shipment');
        $this->request->expects($this->once())->method('getActionName')->willReturn('view');
        $this->restClient
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new HttpResponseException($jsonResponse));

        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $shipmentIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        $searchResult = $this->shipmentRepository->getList($searchCriteriaBuilder->create());

        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $searchResult */
        $this->assertInstanceOf(\Magento\Sales\Model\ResourceModel\Order\Shipment\Collection::class, $searchResult);
        $this->assertCount(1, $searchResult);
        $this->assertContainsOnlyInstancesOf(\Magento\Sales\Model\Order\Shipment::class, $searchResult);

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $searchResult->getFirstItem();
        $this->shipmentLoader
            ->setData('shipment_id', $shipment->getId())
            ->load();
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoDataFixture createOrderAndShipmentFixture
     * @dataProvider getShipmentResponseDataProvider
     * @param string $jsonResponse
     */
    public function platformShipmentIsLoaded($jsonResponse)
    {
        $shipmentIncrementId = ShippedOrderFixture::getShipmentIncrementId();

        $this->request->expects($this->once())->method('getControllerName')->willReturn('order_shipment');
        $this->request->expects($this->once())->method('getActionName')->willReturn('view');
        $this->restClient
            ->expects($this->once())
            ->method('get')
            ->willReturn($jsonResponse);

        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $shipmentIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        $searchResult = $this->shipmentRepository->getList($searchCriteriaBuilder->create());

        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $searchResult */
        $this->assertInstanceOf(\Magento\Sales\Model\ResourceModel\Order\Shipment\Collection::class, $searchResult);
        $this->assertCount(1, $searchResult);
        $this->assertContainsOnlyInstancesOf(\Magento\Sales\Model\Order\Shipment::class, $searchResult);

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $searchResult->getFirstItem();
        $this->shipmentLoader
            ->setData('shipment_id', $shipment->getId())
            ->load();

        /** @var ShipmentProviderInterface $shipmentProvider */
        $shipmentProvider = $this->objectManager->get(ShipmentProviderInterface::class);
        $apiShipment = $shipmentProvider->getShipment();
        $this->assertNotNull($apiShipment);
        $this->assertInstanceOf(\Temando\Shipping\Model\ShipmentInterface::class, $apiShipment);
    }
}
