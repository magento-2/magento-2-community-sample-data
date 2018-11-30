<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Authentication;

use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Temando\Shipping\Rest\AuthAdapter;
use Temando\Shipping\Rest\Authentication;
use Zend\Http\Request;

/**
 * SaveCredentialsTest
 *
 * @magentoAppArea adminhtml
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class RefreshTokenTest extends AbstractBackendController
{
    /**
     * The resource used to authorize action
     *
     * @var string
     */
    protected $resource = 'Magento_Sales::sales';

    /**
     * The uri at which to access the controller
     *
     * @var string
     */
    protected $uri = 'backend/temando/authentication/token';

    /**
     * @test
     */
    public function nonAjaxRequestForbidden()
    {
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isForbidden());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     * @magentoConfigFixture default/carriers/temando/bearer_token_expiry 1999-01-19T03:03:33.000Z
     */
    public function refreshTokenRequestSuccess()
    {
        $sessionToken = 'foo';
        $sessionTokenExpiry = 'bar';

        $authResponse = new DataObject([
            'attributes' => new DataObject([
                'session_token' => $sessionToken,
                'expiry' => $sessionTokenExpiry,
            ]),
        ]);
        $adapterMock = $this->getMockBuilder(AuthAdapter::class)
            ->setMethods(['startSession'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->once())->method('startSession')->willReturn($authResponse);

        $auth = Bootstrap::getObjectManager()->create(Authentication::class, [
            'apiAdapter' => $adapterMock
        ]);
        Bootstrap::getObjectManager()->addSharedInstance($auth, Authentication::class);

        /** @var \Zend\Http\Headers $headers */
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->getRequest()->setHeaders($headers);
        $this->dispatch($this->uri);

        $responseJson = json_decode($this->getResponse()->getBody());
        $this->assertEquals($sessionToken, $responseJson->temando_api_token);
        $this->assertEquals($sessionTokenExpiry, $responseJson->temando_api_token_ttl);
    }

    /**
     * @test
     */
    public function refreshTokenNotNecessary()
    {
        $sessionToken = 'foo';
        $sessionTokenExpiry = '2038-01-19T03:03:33.000Z';

        /** @var \Magento\Backend\Model\Session $session */
        $session = Bootstrap::getObjectManager()->get(\Magento\Backend\Model\Session::class);
        $session->setData(Authentication::DATA_KEY_SESSION_TOKEN, $sessionToken);
        $session->setData(Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, $sessionTokenExpiry);

        $adapterMock = $this->getMockBuilder(AuthAdapter::class)
            ->setMethods(['startSession'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->never())->method('startSession');

        $auth = Bootstrap::getObjectManager()->create(Authentication::class, [
            'apiAdapter' => $adapterMock,
            'session' => $session,
        ]);
        Bootstrap::getObjectManager()->addSharedInstance($auth, Authentication::class);

        /** @var \Zend\Http\Headers $headers */
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->getRequest()->setHeaders($headers);
        $this->dispatch($this->uri);

        $responseJson = json_decode($this->getResponse()->getBody());
        $this->assertEquals($sessionToken, $responseJson->temando_api_token);
        $this->assertEquals($sessionTokenExpiry, $responseJson->temando_api_token_ttl);
    }

    /**
     * @test
     */
    public function refreshTokenRequestFailure()
    {
        $this->expectExceptionMessage('required');

        /** @var \Zend\Http\Headers $headers */
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->getRequest()->setHeaders($headers);

        $this->dispatch($this->uri);
    }

    public function testAclHasAccess()
    {
        $authMock = $this->getMockBuilder(Authentication::class)
            ->setMethods(['connect', 'getSessionToken', 'getSessionTokenExpiry'])
            ->disableOriginalConstructor()
            ->getMock();
        Bootstrap::getObjectManager()->addSharedInstance($authMock, Authentication::class);

        /** @var \Zend\Http\Headers $headers */
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->getRequest()->setHeaders($headers);

        parent::testAclHasAccess();
    }

    public function testAclNoAccess()
    {
        /** @var \Zend\Http\Headers $headers */
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->getRequest()->setHeaders($headers);

        parent::testAclNoAccess();
    }
}
