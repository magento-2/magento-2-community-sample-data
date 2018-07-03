<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

use Magento\TestFramework\Helper\Bootstrap;
use Psr\Log\NullLogger;
use Temando\Shipping\Rest\Adapter as RestAdapter;
use Temando\Shipping\Rest\Request\AuthRequestInterface;
use Temando\Shipping\Rest\Request\ItemRequestInterface;
use Temando\Shipping\Rest\Request\ListRequestInterface;
use Temando\Shipping\Rest\Response\Type\SessionResponseType;
use Temando\Shipping\Test\Integration\Provider\RestResponseProvider;
use Temando\Shipping\Webservice\Exception\HttpResponseException;
use Temando\Shipping\Webservice\HttpClient;
use Temando\Shipping\Webservice\HttpClientInterfaceFactory;

/**
 * AuthAdapterTest
 *
 * @magentoAppIsolation enabled
 *
 * @package  Temando\Shipping\Test
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AuthAdapterTest extends \PHPUnit\Framework\TestCase
{
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
    public function startSessionValidationErrorResponseDataProvider()
    {
        return RestResponseProvider::startSessionValidationErrorResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function startSessionInvalidCredentialsResponseDataProvider()
    {
        return RestResponseProvider::startSessionBadRequestResponseDataProvider();
    }

    /**
     * @test
     *
     * @dataProvider startSessionResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/logging_enabled 0
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://api.temando.io/v1/
     *
     * @param string $jsonResponse
     */
    public function startSession($jsonResponse)
    {
        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_200);
        $testResponse->setContent($jsonResponse);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(HttpClient::class, ['client' => $zendClient]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $clientFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($httpClient);

        $restClient = Bootstrap::getObjectManager()->create(RestClient::class, [
            'httpClientFactory' => $clientFactoryMock,
        ]);

        /** @var AuthRequestInterface $request */
        $request = Bootstrap::getObjectManager()->create(AuthRequestInterface::class, [
            'accountId' => 'foo',
            'bearerToken' => 'bar',
            'scope' => AuthenticationInterface::AUTH_SCOPE_ADMIN,
        ]);
        /** @var AuthAdapter $adapter */
        $adapter = Bootstrap::getObjectManager()->create(AuthAdapter::class, [
            'restClient' => $restClient,
        ]);
        $session = $adapter->startSession($request);

        $this->assertInstanceOf(SessionResponseType::class, $session);
        $this->assertNotEmpty($session->getAttributes()->getSessionToken());
        $this->assertNotEmpty($session->getAttributes()->getExpiry());
    }

    /**
     * @test
     * @expectedException \Temando\Shipping\Rest\Exception\AdapterException
     *
     * @dataProvider startSessionValidationErrorResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/logging_enabled 0
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://api.temando.io/v1/
     *
     * @param string $jsonResponse
     */
    public function invalidAccountIdThrowsException($jsonResponse)
    {
        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_422);
        $testResponse->setContent($jsonResponse);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(HttpClient::class, ['client' => $zendClient]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $clientFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($httpClient);

        $restClient = Bootstrap::getObjectManager()->create(RestClient::class, [
            'httpClientFactory' => $clientFactoryMock,
        ]);

        /** @var AuthRequestInterface $request */
        $request = Bootstrap::getObjectManager()->create(AuthRequestInterface::class, [
            'accountId' => 'foo',
            'bearerToken' => 'bar',
            'scope' => AuthenticationInterface::AUTH_SCOPE_ADMIN,
        ]);
        /** @var AuthAdapter $adapter */
        $adapter = Bootstrap::getObjectManager()->create(AuthAdapter::class, [
            'restClient' => $restClient,
        ]);
        $adapter->startSession($request);
    }

    /**
     * @test
     * @expectedException \Temando\Shipping\Rest\Exception\AdapterException
     *
     * @dataProvider startSessionInvalidCredentialsResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/logging_enabled 0
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://api.temando.io/v1/
     *
     * @param string $jsonResponse
     */
    public function invalidBearerTokenThrowsException($jsonResponse)
    {
        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_400);
        $testResponse->setContent($jsonResponse);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(HttpClient::class, ['client' => $zendClient]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $clientFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($httpClient);

        $restClient = Bootstrap::getObjectManager()->create(RestClient::class, [
            'httpClientFactory' => $clientFactoryMock,
        ]);

        /** @var AuthRequestInterface $request */
        $request = Bootstrap::getObjectManager()->create(AuthRequestInterface::class, [
            'accountId' => 'foo',
            'bearerToken' => 'bar',
            'scope' => AuthenticationInterface::AUTH_SCOPE_ADMIN,
        ]);
        /** @var AuthAdapter $adapter */
        $adapter = Bootstrap::getObjectManager()->create(AuthAdapter::class, [
            'restClient' => $restClient,
        ]);
        $adapter->startSession($request);
    }

    /**
     * @test
     *
     * @magentoConfigFixture default/carriers/temando/logging_enabled 0
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://api.temando.io/v1/
     */
    public function missingAuthCredentialsReturnsEmptyList()
    {
        /** @var ListRequestInterface $request */
        $request = Bootstrap::getObjectManager()->create(ListRequestInterface::class, ['offset' => 0, 'limit' => 20]);
        /** @var RestAdapter $adapter */
        $adapter = Bootstrap::getObjectManager()->create(RestAdapter::class, [
            'logger' => new NullLogger(),
        ]);
        $this->assertEmpty($adapter->getCarrierIntegrations($request));
    }

    /**
     * @test
     * @expectedException \Temando\Shipping\Rest\Exception\AdapterException
     *
     * @magentoConfigFixture default/carriers/temando/logging_enabled 0
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://api.temando.io/v1/
     */
    public function missingAuthCredentialsThrowsException()
    {
        /** @var ItemRequestInterface $request */
        $request = Bootstrap::getObjectManager()->create(ItemRequestInterface::class, ['entityId' => 'foo']);
        /** @var RestAdapter $adapter */
        $adapter = Bootstrap::getObjectManager()->create(RestAdapter::class);
        $adapter->getTrackingEvents($request);
    }
}
