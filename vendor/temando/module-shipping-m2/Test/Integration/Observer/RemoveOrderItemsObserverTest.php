<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\View\Layout;
use Magento\Sales\Model\Order;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * RemoveOrderItemsObserverTest
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class RemoveOrderItemsObserverTest extends \PHPUnit\Framework\TestCase
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
     * Init object manager
     */
    public function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->invoker = $this->objectManager->get(\Magento\Framework\Event\InvokerInterface::class);
        $this->observer = $this->objectManager->get(\Magento\Framework\Event\Observer::class);
    }

    protected function tearDown()
    {
        $this->objectManager->removeSharedInstance(RemoveOrderItemsObserver::class);

        parent::tearDown();
    }

    /**
     * @test
     */
    public function currentPageMustNotRemoveBlock()
    {
        $actionName = 'foo';

        $formMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\Create\Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'unsetChild'])
            ->getMock();
        $formMock
            ->expects($this->never())
            ->method('getOrder');
        $formMock
            ->expects($this->never())
            ->method('unsetChild');

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBlock'])
            ->getMock();

        $layoutMock
            ->expects($this->never())
            ->method('getBlock');
        $config = [
            'instance' => RemoveOrderItemsObserver::class,
            'name' => 'temando_remove_order_items',
        ];

        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     */
    public function formBlockIsUnavailable()
    {
        $actionName = 'adminhtml_order_shipment_new';

        $formMock = $this->getMockBuilder(DataObject::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'unsetChild'])
            ->getMock();
        $formMock
            ->expects($this->never())
            ->method('getOrder');
        $formMock
            ->expects($this->never())
            ->method('unsetChild');

        $layoutMock = $this->getMockBuilder(Layout::class)
                           ->disableOriginalConstructor()
                           ->setMethods(['getBlock'])
                           ->getMock();

        $layoutMock
            ->expects($this->once())
            ->method('getBlock')
            ->with('form')
            ->willReturn($formMock);
        $config = [
            'instance' => RemoveOrderItemsObserver::class,
            'name' => 'temando_remove_order_items',
        ];

        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     */
    public function orderWasPlacedWithDefaultCarrier()
    {
        $actionName = 'adminhtml_order_shipment_new';

        $shippingMethod = new DataObject(['carrier_code' => 'foo', 'method' => 'bar']);
        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShippingMethod'])
            ->getMock();
        $orderMock
            ->expects($this->once())
            ->method('getShippingMethod')
            ->with(true)
            ->willReturn($shippingMethod);

        $formMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\Create\Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'unsetChild'])
            ->getMock();
        $formMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);
        $formMock
            ->expects($this->never())
            ->method('unsetChild');

        $layoutMock = $this->getMockBuilder(Layout::class)
                           ->disableOriginalConstructor()
                           ->setMethods(['getBlock'])
                           ->getMock();

        $layoutMock
            ->expects($this->once())
            ->method('getBlock')
            ->with('form')
            ->willReturn($formMock);
        $config = [
            'instance' => RemoveOrderItemsObserver::class,
            'name' => 'temando_remove_order_items',
        ];

        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     */
    public function orderItemsGetRemoved()
    {
        $actionName = 'adminhtml_order_shipment_new';

        $shippingMethod = new DataObject(['carrier_code' => Carrier::CODE, 'method' => 'bar']);
        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShippingMethod'])
            ->getMock();
        $orderMock
            ->expects($this->once())
            ->method('getShippingMethod')
            ->with(true)
            ->willReturn($shippingMethod);

        $formMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\Create\Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'unsetChild'])
            ->getMock();
        $formMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);
        $formMock
            ->expects($this->once())
            ->method('unsetChild')
            ->with('order_items');

        $layoutMock = $this->getMockBuilder(Layout::class)
                           ->disableOriginalConstructor()
                           ->setMethods(['getBlock'])
                           ->getMock();

        $layoutMock
            ->expects($this->once())
            ->method('getBlock')
            ->with('form')
            ->willReturn($formMock);
        $config = [
            'instance' => RemoveOrderItemsObserver::class,
            'name' => 'temando_remove_order_items',
        ];

        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }
}
