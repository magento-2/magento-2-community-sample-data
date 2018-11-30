<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Dispatch;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Model\DispatchInterface;
use Temando\Shipping\Model\ResourceModel\Repository\DispatchRepositoryInterface;
use Temando\Shipping\Model\Config\ModuleConfig;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Test\Integration\Provider\RestResponseProvider;
use Temando\Shipping\Webservice\HttpClientInterfaceFactory;

/**
 * Temando Dispatch Repository Test
 *
 * @magentoAppIsolation enabled
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function getCompletionResponseDataProvider()
    {
        return RestResponseProvider::getCompletionResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function getCompletionsResponseDataProvider()
    {
        return RestResponseProvider::getCompletionsResponseDataProvider();
    }

    /**
     * Set valid session token
     */
    protected function setUp()
    {
        parent::setUp();

        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->setData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY, '2038-01-19T03:03:33.000Z');
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
     */
    public function apiListingAccessFails()
    {
        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setNextRequestWillFail(true);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(\Temando\Shipping\Webservice\HttpClient::class, [
            'client' => $zendClient,
        ]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($httpClient);
        Bootstrap::getObjectManager()->addSharedInstance($clientFactoryMock, HttpClientInterfaceFactory::class);

        /** @var DispatchRepositoryInterface $dispatchRepository */
        $dispatchRepository = Bootstrap::getObjectManager()->get(DispatchRepositoryInterface::class);
        $items = $dispatchRepository->getList();

        $this->assertInternalType('array', $items);
        $this->assertCount(0, $items);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function apiItemAccessFails()
    {
        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setNextRequestWillFail(true);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(\Temando\Shipping\Webservice\HttpClient::class, [
            'client' => $zendClient,
        ]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($httpClient);
        Bootstrap::getObjectManager()->addSharedInstance($clientFactoryMock, HttpClientInterfaceFactory::class);

        /** @var DispatchRepositoryInterface $dispatchRepository */
        $dispatchRepository = Bootstrap::getObjectManager()->get(DispatchRepositoryInterface::class);
        $dispatchRepository->getById('foo');
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     */
    public function apiListingReturnsError()
    {
        $responseBody = '{"message":"Internal server error"}';
        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_500);
        $testResponse->setContent($responseBody);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(\Temando\Shipping\Webservice\HttpClient::class, [
            'client' => $zendClient,
        ]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($httpClient);
        Bootstrap::getObjectManager()->addSharedInstance($clientFactoryMock, HttpClientInterfaceFactory::class);

        /** @var DispatchRepositoryInterface $dispatchRepository */
        $dispatchRepository = Bootstrap::getObjectManager()->get(DispatchRepositoryInterface::class);
        $items = $dispatchRepository->getList();

        $this->assertInternalType('array', $items);
        $this->assertCount(0, $items);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function apiItemReturnsError()
    {
        $responseBody = '{"message":"Internal server error"}';
        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_500);
        $testResponse->setContent($responseBody);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(\Temando\Shipping\Webservice\HttpClient::class, [
            'client' => $zendClient,
        ]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($httpClient);
        Bootstrap::getObjectManager()->addSharedInstance($clientFactoryMock, HttpClientInterfaceFactory::class);

        /** @var DispatchRepositoryInterface $dispatchRepository */
        $dispatchRepository = Bootstrap::getObjectManager()->get(DispatchRepositoryInterface::class);
        $dispatchRepository->getById('foo');
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     */
    public function apiListingReturnsEmptyResult()
    {
        $responseBody = '{"data":[]}';
        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_200);
        $testResponse->setContent($responseBody);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(\Temando\Shipping\Webservice\HttpClient::class, [
            'client' => $zendClient,
        ]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($httpClient);
        Bootstrap::getObjectManager()->addSharedInstance($clientFactoryMock, HttpClientInterfaceFactory::class);

        /** @var DispatchRepositoryInterface $dispatchRepository */
        $dispatchRepository = Bootstrap::getObjectManager()->get(DispatchRepositoryInterface::class);
        $items = $dispatchRepository->getList();

        $this->assertInternalType('array', $items);
        $this->assertCount(0, $items);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apiItemReturnsEmptyResult()
    {
        $responseBody = '{"errors":[{"status":404,"title":"Completion \'foo\' not found","code":"InternalError"}]}';
        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_404);
        $testResponse->setContent($responseBody);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(\Temando\Shipping\Webservice\HttpClient::class, [
            'client' => $zendClient,
        ]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($httpClient);
        Bootstrap::getObjectManager()->addSharedInstance($clientFactoryMock, HttpClientInterfaceFactory::class);

        /** @var DispatchRepositoryInterface $dispatchRepository */
        $dispatchRepository = Bootstrap::getObjectManager()->get(DispatchRepositoryInterface::class);
        $dispatchRepository->getById('foo');
    }

    /**
     * @test
     * @dataProvider getCompletionsResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $responseBody
     */
    public function getList($responseBody)
    {
        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_200);
        $testResponse->setContent($responseBody);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(\Temando\Shipping\Webservice\HttpClient::class, [
            'client' => $zendClient,
        ]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($httpClient);
        Bootstrap::getObjectManager()->addSharedInstance($clientFactoryMock, HttpClientInterfaceFactory::class);

        /** @var DispatchRepositoryInterface $dispatchRepository */
        $dispatchRepository = Bootstrap::getObjectManager()->get(DispatchRepositoryInterface::class);
        $items = $dispatchRepository->getList();

        $this->assertInternalType('array', $items);
        $this->assertCount(3, $items);
        $this->assertContainsOnly(DispatchInterface::class, $items);
    }

    /**
     * @test
     * @dataProvider getCompletionResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $responseBody
     */
    public function getById($responseBody)
    {
        $completionId = '444cc444-ffff-dddd-eeee-bbbaaddd2000';

        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_200);
        $testResponse->setContent($responseBody);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(\Temando\Shipping\Webservice\HttpClient::class, [
            'client' => $zendClient,
        ]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($httpClient);
        Bootstrap::getObjectManager()->addSharedInstance($clientFactoryMock, HttpClientInterfaceFactory::class);

        /** @var DispatchRepositoryInterface $dispatchRepository */
        $dispatchRepository = Bootstrap::getObjectManager()->get(DispatchRepositoryInterface::class);
        $dispatch = $dispatchRepository->getById($completionId);
        $this->assertInstanceOf(DispatchInterface::class, $dispatch);
        $this->assertEquals($completionId, $dispatch->getDispatchId());
        $this->assertEquals('processed', $dispatch->getStatus());
    }
}
