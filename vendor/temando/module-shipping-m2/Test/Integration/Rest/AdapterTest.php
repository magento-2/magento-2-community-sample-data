<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Rest\Adapter as RestAdapter;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\AuthRequestInterface;
use Temando\Shipping\Rest\Request\ItemRequestInterface;
use Temando\Shipping\Rest\Request\ListRequestInterface;
use Temando\Shipping\Rest\Request\OrderRequestInterface;
use Temando\Shipping\Rest\Request\Type\OrderRequestType;
use Temando\Shipping\Rest\Response\CreateOrderInterface;
use Temando\Shipping\Rest\Response\Type\CarrierIntegrationResponseType;
use Temando\Shipping\Rest\Response\Type\CompletionResponseType;
use Temando\Shipping\Rest\Response\Type\ContainerResponseType;
use Temando\Shipping\Rest\Response\Type\LocationResponseType;
use Temando\Shipping\Rest\Response\Type\SessionResponseType;
use Temando\Shipping\Rest\Response\Type\ShipmentResponseType;
use Temando\Shipping\Rest\Response\UpdateOrder as UpdateOrderResponse;
use Temando\Shipping\Test\Integration\Provider\RestResponseProvider;
use Temando\Shipping\Webservice\HttpClient;
use Temando\Shipping\Webservice\HttpClientInterfaceFactory;

/**
 * AdapterTest
 *
 * @magentoAppIsolation enabled
 * @markTestIncomplete
 *
 * @package  Temando\Shipping\Test
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AdapterTest extends \PHPUnit\Framework\TestCase
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
     * @var HttpClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpClient;

    /**
     * @var Authentication|\PHPUnit_Framework_MockObject_MockObject
     */
    private $auth;

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function startSessionResponseDataProvider()
    {
        return RestResponseProvider::startSessionResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function getCarriersResponseDataProvider()
    {
        return RestResponseProvider::getCarriersResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function getLocationsResponseDataProvider()
    {
        return RestResponseProvider::getLocationsResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function getContainersResponseDataProvider()
    {
        return RestResponseProvider::getContainersResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function getCompletionsDataProvider()
    {
        return RestResponseProvider::getCompletionsResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function getCompletionDataProvider()
    {
        return RestResponseProvider::getCompletionResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function getShipmentResponseDataProvider()
    {
        return RestResponseProvider::getShipmentResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function createOrderResponseProvider()
    {
        return RestResponseProvider::createOrderResponseProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function manifestOrderResponseProvider()
    {
        return RestResponseProvider::updateOrderResponseProvider();
    }

    /**
     * Init object manager
     */
    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();

        $this->auth = $this->getMockBuilder(Authentication::class)
            ->disableOriginalConstructor()
            ->setMethods(['connect', 'getSessionToken'])
            ->getMock();

        $this->httpClient = $this->getMockBuilder(HttpClient::class)
            ->setMethods(['send'])
            ->setConstructorArgs(['client' => new \Zend\Http\Client()])
            ->getMock();

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $clientFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->httpClient);

        $this->restClient = $this->objectManager->create(RestClient::class, [
            'httpClientFactory' => $clientFactoryMock,
        ]);
    }

    /**
     * @test
     *
     * @dataProvider startSessionResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/logging_enabled 0
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     *
     * @param string $jsonResponse
     */
    public function startSession($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);

        /** @var AuthRequestInterface $request */
        $request = $this->objectManager->create(AuthRequestInterface::class, [
            'username' => '',
            'password' => '',
            'accountId' => 'foo',
            'bearerToken' => 'bar',
            'scope' => AuthenticationInterface::AUTH_SCOPE_ADMIN,
        ]);
        /** @var AuthAdapter $adapter */
        $adapter = $this->objectManager->create(AuthAdapter::class, [
            'restClient' => $this->restClient,
        ]);
        $session = $adapter->startSession($request);

        $this->assertInstanceOf(SessionResponseType::class, $session);
        $this->assertNotEmpty($session->getAttributes()->getSessionToken());
        $this->assertNotEmpty($session->getAttributes()->getExpiry());
    }

    /**
     * @test
     * @dataProvider getCarriersResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $jsonResponse
     */
    public function getCarriers($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);

        /** @var ListRequestInterface $request */
        $request = $this->objectManager->create(ListRequestInterface::class, ['offset' => 0, 'limit' => 20]);
        /** @var RestAdapter $adapter */
        $adapter = $this->objectManager->create(RestAdapter::class, [
            'auth' => $this->auth,
            'restClient' => $this->restClient,
        ]);
        $carriers = $adapter->getCarrierIntegrations($request);

        $this->assertInternalType('array', $carriers);
        $this->assertContainsOnly(CarrierIntegrationResponseType::class, $carriers);
    }

    /**
     * @test
     * @dataProvider getLocationsResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $jsonResponse
     */
    public function getLocations($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);

        /** @var ListRequestInterface $request */
        $request = $this->objectManager->create(ListRequestInterface::class, ['offset' => 0, 'limit' => 20]);
        /** @var RestAdapter $adapter */
        $adapter = $this->objectManager->create(RestAdapter::class, [
            'auth' => $this->auth,
            'restClient' => $this->restClient,
        ]);
        $locations = $adapter->getLocations($request);

        $this->assertInternalType('array', $locations);
        $this->assertContainsOnly(LocationResponseType::class, $locations);
    }

    /**
     * @test
     * @dataProvider getContainersResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $jsonResponse
     */
    public function getContainers($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);

        /** @var ListRequestInterface $request */
        $request = $this->objectManager->create(ListRequestInterface::class, ['offset' => 0, 'limit' => 20]);
        /** @var RestAdapter $adapter */
        $adapter = $this->objectManager->create(RestAdapter::class, [
            'auth' => $this->auth,
            'restClient' => $this->restClient,
        ]);
        $containers = $adapter->getContainers($request);

        $this->assertInternalType('array', $containers);
        $this->assertContainsOnly(ContainerResponseType::class, $containers);
    }

    /**
     * @test
     * @dataProvider getCompletionsDataProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $jsonResponse
     */
    public function getCompletions($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);

        /** @var ListRequestInterface $request */
        $request = $this->objectManager->create(ListRequestInterface::class, [
            'offset' => 0,
            'limit' => 20,
            'filter' => [['foo' => 'bar']],
        ]);
        /** @var RestAdapter $adapter */
        $adapter = $this->objectManager->create(RestAdapter::class, [
            'auth' => $this->auth,
            'restClient' => $this->restClient,
        ]);
        $completions = $adapter->getCompletions($request);

        $this->assertInternalType('array', $completions);
        $this->assertContainsOnly(CompletionResponseType::class, $completions);
    }

    /**
     * @test
     * @dataProvider getCompletionDataProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $jsonResponse
     */
    public function getCompletion($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);

        $completionId = '444cc444-ffff-dddd-eeee-bbbaaddd2000';
        /** @var ItemRequestInterface $request */
        $request = $this->objectManager->create(ItemRequestInterface::class, [
            'entityId' => $completionId
        ]);

        /** @var RestAdapter $adapter */
        $adapter = $this->objectManager->create(RestAdapter::class, [
            'auth' => $this->auth,
            'restClient' => $this->restClient,
        ]);
        $completion = $adapter->getCompletion($request);

        $this->assertInstanceOf(CompletionResponseType::class, $completion);
        $this->assertEquals($completionId, $completion->getId());
    }

    /**
     * @test
     */
    public function getTracking()
    {
        $this->markTestIncomplete('mock response once API is ready');
    }

    /**
     * @test
     * @dataProvider getShipmentResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $jsonResponse
     */
    public function getShipment($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);

        /** @var ItemRequestInterface $request */
        $shipmentId = '00000000-5000-0005-0000-000000000000';
        $request =  $this->objectManager->create(ItemRequestInterface::class, [
            'entityId' => $shipmentId,
        ]);
        /** @var RestAdapter $adapter */
        $adapter = $this->objectManager->create(RestAdapter::class, [
            'auth' => $this->auth,
            'restClient' => $this->restClient,
        ]);
        $shipment = $adapter->getShipment($request);

        $this->assertInstanceOf(ShipmentResponseType::class, $shipment);
        $this->assertEquals($shipmentId, $shipment->getId());

        // assert origin location being parsed
        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getContact()->getOrganisationName());
        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getContact()->getPersonLastName());
        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getContact()->getPersonFirstName());
        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getContact()->getEmail());
        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getContact()->getPhoneNumber());

        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getAddress()->getCountryCode());
        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getAddress()->getLines());
        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getAddress()->getPostalCode());
        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getAddress()->getLocality());
        $this->assertNotEmpty($shipment->getAttributes()->getOrigin()->getAddress()->getAdministrativeArea());

        // assert documentation being parsed
        $packages = $shipment->getAttributes()->getPackages();
        $this->assertNotEmpty($packages);
        $this->assertContainsOnlyInstancesOf(
            \Temando\Shipping\Rest\Response\Type\Generic\Package::class,
            $packages
        );
        foreach ($packages as $package) {
            $packageDocs = $package->getDocumentation();
            $this->assertContainsOnlyInstancesOf(
                \Temando\Shipping\Rest\Response\Type\Generic\Documentation::class,
                $packageDocs
            );
            foreach ($packageDocs as $packageDoc) {
                $this->assertNotEmpty($packageDoc->getSize());
                $this->assertNotEmpty($packageDoc->getDescription());
                $this->assertNotEmpty($packageDoc->getId());
                $this->assertNotEmpty($packageDoc->getMimeType());
                $this->assertNotEmpty($packageDoc->getEncoding());
                $this->assertNotEmpty($packageDoc->getType());
                $this->assertNotEmpty($packageDoc->getUrl());
            }
        }
    }

    /**
     * @test
     * @dataProvider createOrderResponseProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $jsonResponse
     */
    public function createOrder($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);

        $orderType = $this->getMockBuilder(OrderRequestType::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var OrderRequestInterface $request */
        $request =  $this->objectManager->create(OrderRequestInterface::class, [
            'order' => $orderType,
        ]);
        /** @var OrderAdapter $adapter */
        $adapter = $this->objectManager->create(OrderAdapter::class, [
            'auth' => $this->auth,
            'restClient' => $this->restClient,
        ]);
        $order = $adapter->createOrder($request);

        $this->assertInstanceOf(CreateOrderInterface::class, $order);
        $this->assertNotEmpty($order->getData()->getId());
        $this->assertNotEmpty($order->getData()->getAttributes()->getSource()->getReference());

        foreach ($order->getIncluded() as $orderQualification) {
            $shippingExperiences = $orderQualification->getAttributes()->getExperiences();
            $this->assertInternalType('array', $shippingExperiences);
            $this->assertNotEmpty($shippingExperiences);
            foreach ($shippingExperiences as $shippingExperience) {
                $this->assertInternalType('string', $shippingExperience->getCode());
                $this->assertNotEmpty($shippingExperience->getCode());

                $this->assertInternalType('array', $shippingExperience->getCost());
                $this->assertNotEmpty($shippingExperience->getCost());
                foreach ($shippingExperience->getCost() as $localizedCost) {
                    $this->assertNotEmpty($localizedCost->getAmount());
                    $this->assertNotEmpty($localizedCost->getCurrency());
                }

                $this->assertInternalType('array', $shippingExperience->getDescription());
                $this->assertNotEmpty($shippingExperience->getDescription());
                foreach ($shippingExperience->getDescription() as $localizedDescription) {
                    $this->assertNotEmpty($localizedDescription->getLocale());
                    $this->assertNotEmpty($localizedDescription->getText());
                }
            }
        }
    }

    /**
     * @test
     * @dataProvider manifestOrderResponseProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $jsonResponse
     */
    public function manifestOrder($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);

        $orderType = $this->getMockBuilder(OrderRequestType::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var OrderRequestInterface $request */
        $request =  $this->objectManager->create(OrderRequestInterface::class, [
            'orderId' => '00000000-0000-0000-0000-000000000000',
            'order' => $orderType,
        ]);
        /** @var OrderAdapter $adapter */
        $adapter = $this->objectManager->create(OrderAdapter::class, [
            'auth' => $this->auth,
            'restClient' => $this->restClient,
        ]);
        $order = $adapter->updateOrder($request);

        $this->assertInstanceOf(UpdateOrderResponse::class, $order);
        $this->assertNotEmpty($order->getData()->getId());
        $this->assertNotEmpty($order->getData()->getAttributes()->getSource()->getReference());
    }
}
