<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\View\Layout;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Model\Shipment\LocationInterface;
use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\Shipment\PackageItemInterface;
use Temando\Shipping\Model\Shipment\ShipmentProvider;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * PrepareShipmentViewObserverTest
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class PrepareShipmentViewObserverTest extends \PHPUnit\Framework\TestCase
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
        Bootstrap::getObjectManager()->removeSharedInstance(PrepareShipmentViewObserver::class);
        Bootstrap::getObjectManager()->removeSharedInstance(ShipmentProvider::class);
        Bootstrap::getObjectManager()->removeSharedInstance(ShipmentProviderInterface\Proxy::class);

        parent::tearDown();
    }

    /**
     * @param string $originCountryId
     * @param string $destinationCountryId
     * @return ShipmentInterface
     */
    private function getShipment($originCountryId = 'US', $destinationCountryId = 'US')
    {
        $origin = Bootstrap::getObjectManager()->create(LocationInterface::class, ['data' => [
            LocationInterface::COUNTRY_CODE => $originCountryId,
        ]]);
        $destination = Bootstrap::getObjectManager()->create(LocationInterface::class, ['data' => [
            LocationInterface::COUNTRY_CODE => $destinationCountryId,
        ]]);
        $packageItem = Bootstrap::getObjectManager()->create(PackageItemInterface::class, ['data' => [
            PackageItemInterface::SKU => 'foo-sku',
        ]]);
        $package = Bootstrap::getObjectManager()->create(PackageInterface::class, ['data' => [
            PackageInterface::PACKAGE_ID => '1',
            PackageInterface::ITEMS => [$packageItem],
        ]]);

        $shipment = Bootstrap::getObjectManager()->create(ShipmentInterface::class, ['data' => [
            ShipmentInterface::SHIPMENT_ID => "00000000-5000-0005-0000-000000000000",
            ShipmentInterface::ORDER_ID => "00000000-3000-0003-0000-000000000000",
            ShipmentInterface::ORIGIN_LOCATION => $origin,
            ShipmentInterface::DESTINATION_LOCATION => $destination,
            ShipmentInterface::PACKAGES => [$package],
        ]]);

        return $shipment;
    }

    /**
     * Some other page than shipment details page gets prepared, no need to
     * change a template here.
     *
     * Assert that observer returns early and does not even attempt to read the
     * form block from arguments.
     *
     * @test
     */
    public function currentPageIsNotViewShipmentPage()
    {
        $actionName = 'foo';

        $shipment = $this->getShipment();
        /** @var ShipmentProvider $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProvider::class);
        $shipmentProvider->setShipment($shipment);

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock
            ->expects($this->never())
            ->method('getBlock');

        $config = [
            'instance' => PrepareShipmentViewObserver::class,
            'name' => 'temando_replace_shipment_template',
        ];
        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * Some shipment with non-temando shipping method was loaded. No Temando
     * Shipment entity was added to the registry.
     *
     * Assert that observer returns early and does not even attempt to read the
     * form block from arguments.
     *
     * @test
     */
    public function currentPageShowsShipmentWithCoreCarrier()
    {
        $actionName = 'adminhtml_order_shipment_view';

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock
            ->expects($this->never())
            ->method('getBlock');

        $config = [
            'instance' => PrepareShipmentViewObserver::class,
            'name' => 'temando_replace_shipment_template',
        ];
        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * We are on shipment details page with a shipment created through
     * Temando API. Some other block gets prepared though, no need to
     * change a template here.
     *
     * Assert that observer returns early and does not attempt to change the
     * block template.
     *
     * @test
     */
    public function wrongBlockType()
    {
        $actionName = 'adminhtml_order_shipment_view';

        $shipment = $this->getShipment();
        /** @var ShipmentProvider $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProvider::class);
        $shipmentProvider->setShipment($shipment);

        $formMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\Create\Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['setTemplate'])
            ->getMock();
        $formMock
            ->expects($this->never())
            ->method('setTemplate');

        $orderInfoMock = $this->getMockBuilder(\Magento\Sales\Block\Adminhtml\Order\View\Info::class)
            ->disableOriginalConstructor()
            ->setMethods(['setTemplate'])
            ->getMock();

        $valueMap = [
            ['form', $formMock],
            ['order_info' , $orderInfoMock],
        ];

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();

        $layoutMock
            ->expects($this->exactly(2))
            ->method('getBlock')
            ->with($this->logicalOr('form', 'order_info'))
            ->willReturnMap($valueMap);

        $config = [
            'instance' => PrepareShipmentViewObserver::class,
            'name' => 'temando_replace_shipment_template',
        ];
        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * Assert template gets updated on form block.
     *
     * @test
     */
    public function formTemplateGetsChangedForDomesticShipment()
    {
        $actionName = 'adminhtml_order_shipment_view';

        $shipment = $this->getShipment();
        /** @var ShipmentProvider $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProvider::class);
        $shipmentProvider->setShipment($shipment);

        $formMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\View\Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['setTemplate'])
            ->getMock();
        $formMock
            ->expects($this->once())
            ->method('setTemplate');

        $orderInfoMock = $this->getMockBuilder(\Magento\Sales\Block\Adminhtml\Order\View\Info::class)
            ->disableOriginalConstructor()
            ->setMethods(['setTemplate'])
            ->getMock();

        $valueMap = [
            ['form', $formMock],
            ['order_info' , $orderInfoMock],
        ];

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock
            ->expects($this->exactly(2))
            ->method('getBlock')
            ->with($this->logicalOr('form', 'order_info'))
            ->willReturnMap($valueMap);

        $config = [
            'instance' => PrepareShipmentViewObserver::class,
            'name' => 'temando_replace_shipment_template',
        ];
        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * Assert template gets updated on form shipment items block.
     *
     * @test
     */
    public function itemsTemplateGetsChangedForInternationalShipment()
    {
        $actionName = 'adminhtml_order_shipment_view';

        $shipment = $this->getShipment('US', 'CA');
        /** @var ShipmentProvider $shipmentProvider */
        $shipmentProvider = Bootstrap::getObjectManager()->get(ShipmentProvider::class);
        $shipmentProvider->setShipment($shipment);

        $formMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\View\Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['setTemplate'])
            ->getMock();
        $formMock
            ->expects($this->once())
            ->method('setTemplate');

        $itemsMock = $this->getMockBuilder(\Magento\Shipping\Block\Adminhtml\View\Items::class)
            ->disableOriginalConstructor()
            ->setMethods(['setTemplate'])
            ->getMock();
        $itemsMock
            ->expects($this->once())
            ->method('setTemplate');

        $orderInfoMock = $this->getMockBuilder(\Magento\Sales\Block\Adminhtml\Order\View\Info::class)
            ->disableOriginalConstructor()
            ->setMethods(['setTemplate'])
            ->getMock();

        $valueMap = [
            ['form', $formMock],
            ['order_info' , $orderInfoMock],
            ['shipment_items', $itemsMock],
        ];

        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock
            ->expects($this->exactly(3))
            ->method('getBlock')
            ->with($this->logicalOr('form', 'order_info', 'shipment_items'))
            ->willReturnMap($valueMap);

        $config = [
            'instance' => PrepareShipmentViewObserver::class,
            'name' => 'temando_replace_shipment_template',
        ];
        $this->observer->addData([
            'full_action_name' => $actionName,
            'layout' => $layoutMock,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }
}
