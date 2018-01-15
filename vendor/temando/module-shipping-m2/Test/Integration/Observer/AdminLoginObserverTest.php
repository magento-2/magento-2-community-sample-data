<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Message\Manager as MessageManager;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Webservice\HttpClientInterfaceFactory;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\Rest\AuthAdapter as RestAdapter;
use Temando\Shipping\Rest\Authentication;
use Temando\Shipping\Rest\RestClient;
use Temando\Shipping\Test\Integration\Provider\RestResponseProvider;
use Temando\Shipping\Webservice\Exception\HttpResponseException;
use Temando\Shipping\Webservice\HttpClient;

/**
 * AdminLoginObserverTest
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AdminLoginObserverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\Event\Invoker\InvokerDefault
     */
    private $invoker;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    private $observer;

    /**
     * @var MessageManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManager;

    /**
     * @var HttpClientInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpClientFactory;

    /**
     * @var HttpClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpClient;

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function startSessionSuccessResponseDataProvider()
    {
        return RestResponseProvider::startSessionResponseDataProvider();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function startSessionFailureResponseDataProvider()
    {
        return RestResponseProvider::startSessionValidationErrorResponseDataProvider();
    }

    /**
     * Init object manager
     */
    public function setUp()
    {
        parent::setUp();

        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->setData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY, '1999-01-19T03:03:33.000Z');

        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $this->invoker = $objectManager->get(\Magento\Framework\Event\InvokerInterface::class);
        $this->observer = $objectManager->get(\Magento\Framework\Event\Observer::class);

        $carrierMock = $this->getMockBuilder(Carrier::class)
            ->setMethods(['getConfigFlag'])
            ->disableOriginalConstructor()
            ->getMock();
        $carrierMock->expects($this->once())
            ->method('getConfigFlag')
            ->with('active')
            ->willReturn(($this->getName(false) !== 'carrierIsNotActive'));
        $objectManager->addSharedInstance($carrierMock, Carrier::class);

        // prepare the http connection to be mocked
        $this->httpClient = $this->getMockBuilder(HttpClient::class)
            ->setMethods(['send'])
            ->setConstructorArgs(['client' => new \Zend\Http\Client()])
            ->getMock();

        $this->httpClientFactory = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageManager = $this->getMockBuilder(MessageManager::class)
            ->setMethods(['addWarningMessage', 'addExceptionMessage'])
            ->disableOriginalConstructor()
            ->getMock();
        $objectManager->addSharedInstance($this->messageManager, MessageManager::class);

        $restClient = $objectManager->create(RestClient::class, [
            'httpClientFactory' => $this->httpClientFactory,
        ]);
        $objectManager->addSharedInstance($restClient, RestClient::class);
    }

    protected function tearDown()
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        /** @var SessionManagerInterface $adminSession */
        $adminSession = $objectManager->get(SessionManagerInterface::class);
        $adminSession->unsetData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY);

        $objectManager->removeSharedInstance(AdminLoginObserver::class);
        $objectManager->removeSharedInstance(Authentication::class);
        $objectManager->removeSharedInstance(RestAdapter::class);

        parent::tearDown();
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture default/carriers/temando/account_id accountId
     * @magentoConfigFixture default/carriers/temando/bearer_token bearerToken
     */
    public function carrierIsNotActive()
    {
        $this->httpClient
            ->expects($this->never())
            ->method('send');
        $this->httpClientFactory
            ->expects($this->never())
            ->method('create');

        $this->messageManager
            ->expects($this->never())
            ->method('addWarningMessage');
        $this->messageManager
            ->expects($this->never())
            ->method('addExceptionMessage');

        $config = [
            'instance' => AdminLoginObserver::class,
            'name' => 'temando_admin_login',
        ];
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function carrierIsActiveButCredentialsAreMissing()
    {
        $this->httpClient
            ->expects($this->never())
            ->method('send');
        $this->httpClientFactory
            ->expects($this->never())
            ->method('create');

        $this->messageManager
            ->expects($this->once())
            ->method('addWarningMessage');
        $this->messageManager
            ->expects($this->never())
            ->method('addExceptionMessage');

        $config = [
            'instance' => AdminLoginObserver::class,
            'name' => 'temando_admin_login',
        ];
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture default/carriers/temando/bearer_token foo
     * @magentoConfigFixture default/carriers/temando/account_id bar
     * @dataProvider startSessionSuccessResponseDataProvider
     *
     * @param string $jsonResponse
     */
    public function sessionRefreshSuccess($jsonResponse)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($jsonResponse);
        $this->httpClientFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->httpClient);

        $this->messageManager
            ->expects($this->never())
            ->method('addWarningMessage');
        $this->messageManager
            ->expects($this->never())
            ->method('addExceptionMessage');

        $config = [
            'instance' => AdminLoginObserver::class,
            'name' => 'temando_admin_login',
        ];
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture default/carriers/temando/bearer_token foo
     * @magentoConfigFixture default/carriers/temando/account_id bar
     * @dataProvider startSessionFailureResponseDataProvider
     *
     * @param string $jsonResponse
     */
    public function sessionRefreshFailure($jsonResponse)
    {
        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->setData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY, '1999-01-19T03:03:33.000Z');

        $httpException = new HttpResponseException($jsonResponse);
        $this->httpClient
            ->expects($this->once())
            ->method('send')
            ->willThrowException($httpException);
        $this->httpClientFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->httpClient);

        $this->messageManager
            ->expects($this->never())
            ->method('addWarningMessage');
        $this->messageManager
            ->expects($this->once())
            ->method('addExceptionMessage');

        $config = [
            'instance' => AdminLoginObserver::class,
            'name' => 'temando_admin_login',
        ];
        $this->invoker->dispatch($config, $this->observer);
    }
}
