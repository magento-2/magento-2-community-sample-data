<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Rest\Authentication;

/**
 * AdminLogoutObserverTest
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AdminLogoutObserverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Event\Invoker\InvokerDefault
     */
    private $invoker;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    private $observer;

    /**
     * @var Authentication|\PHPUnit_Framework_MockObject_MockObject
     */
    private $auth;

    /**
     * Init object manager
     */
    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->invoker = $this->objectManager->get(\Magento\Framework\Event\InvokerInterface::class);
        $this->observer = $this->objectManager->get(\Magento\Framework\Event\Observer::class);

        $this->auth = $this->getMockBuilder(Authentication::class)
            ->setMethods(['disconnect'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager->addSharedInstance($this->auth, Authentication::class);
    }

    protected function tearDown()
    {
        $this->objectManager->removeSharedInstance(AdminLogoutObserver::class);
        $this->objectManager->removeSharedInstance(Authentication::class);

        parent::tearDown();
    }

    /**
     * @test
     */
    public function invalidateSessionToken()
    {
        $this->auth
            ->expects($this->once())
            ->method('disconnect');

        $config = [
            'instance' => AdminLogoutObserver::class,
            'name' => 'temando_admin_logout',
        ];
        $this->invoker->dispatch($config, $this->observer);
    }
}
