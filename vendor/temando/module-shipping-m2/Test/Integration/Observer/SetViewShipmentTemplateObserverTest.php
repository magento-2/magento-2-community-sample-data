<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\View\Layout;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * SetViewShipmentTemplateObserverTest
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class SetViewShipmentTemplateObserverTest extends \PHPUnit\Framework\TestCase
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
     * Init object manager
     */
    public function setUp()
    {
        parent::setUp();

        $this->invoker = Bootstrap::getObjectManager()->get(\Magento\Framework\Event\InvokerInterface::class);
        $this->observer = Bootstrap::getObjectManager()->get(\Magento\Framework\Event\Observer::class);
    }

    protected function tearDown()
    {
        Bootstrap::getObjectManager()->removeSharedInstance(SetViewShipmentTemplateObserver::class);

        parent::tearDown();
    }

    /**
     * @test
     */
    public function currentPageIsNotViewShipmentPage()
    {
        $actionName = 'foo';

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock
            ->expects($this->never())
            ->method('getBlock');

        $config = [
            'instance' => SetViewShipmentTemplateObserver::class,
            'name' => 'temando_replace_shipment_template',
        ];
        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     */
    public function wrongBlockType()
    {
        $actionName = 'adminhtml_order_shipment_view';

        $formMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\Create\Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder'])
            ->getMock();
        $formMock
            ->expects($this->never())
            ->method('getOrder');

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock
            ->expects($this->once())
            ->method('getBlock')
            ->willReturn($formMock);

        $config = [
            'instance' => SetViewShipmentTemplateObserver::class,
            'name' => 'temando_replace_shipment_template',
        ];
        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     */
    public function currentOrderWasNotShippedWithTemando()
    {
        $actionName = 'adminhtml_order_shipment_view';

        $order = Bootstrap::getObjectManager()->create(OrderInterface::class, ['data' => [
            'shipping_method' => 'foo_bar',
        ]]);

        $formMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\View\Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'setTemplate'])
            ->getMock();
        $formMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);
        $formMock
            ->expects($this->never())
            ->method('setTemplate');

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock
            ->expects($this->once())
            ->method('getBlock')
            ->willReturn($formMock);

        $config = [
            'instance' => SetViewShipmentTemplateObserver::class,
            'name' => 'temando_replace_shipment_template',
        ];
        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     */
    public function templateGetsChanged()
    {
        $actionName = 'adminhtml_order_shipment_view';

        $order = Bootstrap::getObjectManager()->create(OrderInterface::class, ['data' => [
            'shipping_method' => 'temando_bar',
        ]]);

        $formMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\View\Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'setTemplate'])
            ->getMock();
        $formMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);
        $formMock
            ->expects($this->once())
            ->method('setTemplate');

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock
            ->expects($this->once())
            ->method('getBlock')
            ->willReturn($formMock);

        $config = [
            'instance' => SetViewShipmentTemplateObserver::class,
            'name' => 'temando_replace_shipment_template',
        ];
        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }
}
