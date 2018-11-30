<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

use Magento\Backend\Model\Session;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Session\Storage;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Response\Type\SessionResponseType;

/**
 * Temando Session Handling Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AuthServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storageMock;

    /**
     * @var SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionManager;

    /**
     * @return string[]
     */
    public function invalidCredentialsDataProvider()
    {
        return [
            'no_credentials' => [null, null],
            'no_account_id' => ['23', null],
            'no_bearer_token' => [null, '808'],
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->storageMock = $this->getMockBuilder(Storage::class)
            ->setMethods(['getData', 'setData'])
            ->getMock();
        $this->sessionManager = Bootstrap::getObjectManager()->create(
            SessionManagerInterface::class,
            ['storage' => $this->storageMock]
        );
    }

    /**
     * Assert token being requested from API if there is no expiry date available.
     *
     * @test
     */
    public function sessionTokenExpiryDateUnavailable()
    {
        $currentTokenExpiry = null;

        $newSessionToken = 'foo';
        $newSessionTokenExpiry = '2038';

        $newSessionResponseAttributes = new \Temando\Shipping\Rest\Response\Type\Session\Attributes();
        $newSessionResponseAttributes->setSessionToken($newSessionToken);
        $newSessionResponseAttributes->setExpiry($newSessionTokenExpiry);
        $newSessionResponse = new SessionResponseType();
        $newSessionResponse->setAttributes($newSessionResponseAttributes);

        $this->storageMock->expects($this->once())
            ->method('getData')
            ->with(Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, null)
            ->willReturn($currentTokenExpiry);
        $this->storageMock->expects($this->exactly(2))
            ->method('setData')
            ->withConsecutive(
                [Authentication::DATA_KEY_SESSION_TOKEN, $newSessionToken],
                [Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, $newSessionTokenExpiry]
            );

//        $storageMock = $this->getMockBuilder(Storage::class)
//            ->setMethods(['getData', 'setData'])
//            ->getMock();
//        $storageMock->expects($this->once())
//            ->method('getData')
//            ->with(Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, null)
//            ->willReturn($currentTokenExpiry);
//        $storageMock->expects($this->exactly(2))
//            ->method('setData')
//            ->withConsecutive(
//                [Authentication::DATA_KEY_SESSION_TOKEN, $newSessionToken],
//                [Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, $newSessionTokenExpiry]
//            );
//        $session = Bootstrap::getObjectManager()->create(Session::class, ['storage' => $storageMock]);

        $adapterMock = $this->getMockBuilder(AuthAdapter::class)
            ->setMethods(['startSession'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('startSession')
            ->willReturn($newSessionResponse);

        /** @var Authentication $auth */
        $auth = Bootstrap::getObjectManager()->create(Authentication::class, [
            'session' => $this->sessionManager,
            'apiAdapter' => $adapterMock,
        ]);

        $auth->connect('foo', 'bar');
    }

    /**
     * Assert AuthenticationException being thrown when API returns error.
     *
     * @test
     * @expectedException \Magento\Framework\Exception\AuthenticationException
     */
    public function sessionTokenRefreshFails()
    {
        $currentTokenExpiry = '1999-01-19T03:03:33.000Z';
        $exceptionMessage = 'error foo';

        $this->storageMock->expects($this->once())
            ->method('getData')
            ->with(Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, null)
            ->willReturn($currentTokenExpiry);
        $this->storageMock->expects($this->never())
            ->method('setData');

        $adapterMock = $this->getMockBuilder(AuthAdapter::class)
            ->setMethods(['startSession'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('startSession')
            ->willThrowException(new AdapterException($exceptionMessage));

        /** @var Authentication $auth */
        $auth = Bootstrap::getObjectManager()->create(Authentication::class, [
            'session' => $this->sessionManager,
            'apiAdapter' => $adapterMock,
        ]);

        $auth->connect('foo', 'bar');
    }

    /**
     * @test
     */
    public function sessionTokenExpired()
    {
        $currentTokenExpiry = '1999-01-19T03:03:33.000Z';

        $newSessionToken = 'foo';
        $newSessionTokenExpiry = '2038';

        $newSessionResponseAttributes = new \Temando\Shipping\Rest\Response\Type\Session\Attributes();
        $newSessionResponseAttributes->setSessionToken($newSessionToken);
        $newSessionResponseAttributes->setExpiry($newSessionTokenExpiry);
        $newSessionResponse = new SessionResponseType();
        $newSessionResponse->setAttributes($newSessionResponseAttributes);

        $this->storageMock->expects($this->once())
            ->method('getData')
            ->with(Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, null)
            ->willReturn($currentTokenExpiry);
        $this->storageMock->expects($this->exactly(2))
            ->method('setData')
            ->withConsecutive(
                [Authentication::DATA_KEY_SESSION_TOKEN, $newSessionToken],
                [Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, $newSessionTokenExpiry]
            );

        $adapterMock = $this->getMockBuilder(AuthAdapter::class)
            ->setMethods(['startSession'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('startSession')
            ->willReturn($newSessionResponse);

        /** @var Authentication $auth */
        $auth = Bootstrap::getObjectManager()->create(Authentication::class, [
            'session' => $this->sessionManager,
            'apiAdapter' => $adapterMock,
        ]);

        $auth->connect('foo', 'bar');
    }

    /**
     * @test
     */
    public function sessionTokenValid()
    {
        $currentTokenExpiry = '2038-01-19T03:03:33.000Z';

        $this->storageMock->expects($this->once())
            ->method('getData')
            ->with(Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, null)
            ->willReturn($currentTokenExpiry);
        $this->storageMock->expects($this->never())
            ->method('setData');

        $adapterMock = $this->getMockBuilder(AuthAdapter::class)
            ->setMethods(['startSession'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->never())
            ->method('startSession');

        /** @var Authentication $auth */
        $auth = Bootstrap::getObjectManager()->create(Authentication::class, [
            'session' => $this->sessionManager,
            'apiAdapter' => $adapterMock,
        ]);

        $auth->connect('foo', 'bar');
    }

    /**
     * @test
     * @dataProvider invalidCredentialsDataProvider
     * @expectedException \Magento\Framework\Exception\InputException
     *
     * @param string $bearerToken
     * @param string $accountId
     */
    public function credentialsMissing($bearerToken, $accountId)
    {
        $currentTokenExpiry = '1999-01-19T03:03:33.000Z';

        $this->storageMock->expects($this->once())
            ->method('getData')
            ->with(Authentication::DATA_KEY_SESSION_TOKEN_EXPIRY, null)
            ->willReturn($currentTokenExpiry);
        $this->storageMock->expects($this->never())
            ->method('setData');

        /** @var Authentication $auth */
        $auth = Bootstrap::getObjectManager()->create(Authentication::class, [
            'session' => $this->sessionManager,
        ]);

        $auth->connect($bearerToken, $accountId);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function disconnect()
    {
        $currentToken = 'abcde';
        $currentTokenExpiry = '1999-01-19T03:03:33.000Z';

        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->setData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN, $currentToken);
        $adminSession->setData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY, $currentTokenExpiry);

        $adapterMock = $this->getMockBuilder(AuthAdapter::class)
            ->setMethods(['endSession'])
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('endSession');

        /** @var Authentication $auth */
        $auth = Bootstrap::getObjectManager()->create(Authentication::class, [
            'session' => $adminSession,
            'apiAdapter' => $adapterMock,
        ]);

        // before disconnect
        $this->assertEquals($currentToken, $auth->getSessionToken());
        $this->assertEquals($currentTokenExpiry, $auth->getSessionTokenExpiry());

        $auth->disconnect();

        // after disconnect
        $this->assertEmpty($auth->getSessionToken());
        $this->assertEmpty($auth->getSessionTokenExpiry());
    }
}
