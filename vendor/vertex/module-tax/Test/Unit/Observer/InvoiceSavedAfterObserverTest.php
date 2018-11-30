<?php

namespace Vertex\Tax\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Model\Store;
use Vertex\Services\Invoice\Request;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ConfigurationValidator;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\InvoiceSentRegistry;
use Vertex\Tax\Model\TaxInvoice;
use Vertex\Tax\Observer\InvoiceSavedAfterObserver;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InvoiceSavedAfterObserverTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|Config */
    private $configMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ConfigurationValidator */
    private $configValidatorMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CountryGuard */
    private $countryGuardMock;

    /** @var InvoiceSavedAfterObserver */
    private $invoiceSavedAfterObserverMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|InvoiceSentRegistry */
    private $invoiceSentRegistryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ManagerInterface */
    private $managerInterfaceMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxInvoice */
    private $taxInvoiceMock;

    protected function setUp()
    {
        parent::setUp();

        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['isVertexActive', 'requestByInvoiceCreation'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->countryGuardMock = $this->getMockBuilder(CountryGuard::class)
            ->setMethods(['isOrderServiceableByVertex'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->taxInvoiceMock = $this->getMockBuilder(TaxInvoice::class)
            ->setMethods(['prepareInvoiceData', 'sendInvoiceRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->managerInterfaceMock = $this->getMockBuilder(ManagerInterface::class)
            ->setMethods(['addSuccessMessage'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->configValidatorMock = $this->getMockBuilder(ConfigurationValidator::class)
            ->setMethods(['execute'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = new ConfigurationValidator\Result();
        $result->setValid(true);

        $this->configValidatorMock->method('execute')
            ->willReturn($result);

        $this->invoiceSentRegistryMock = $this->createMock(InvoiceSentRegistry::class);

        $this->invoiceSavedAfterObserverMock = $this->getObject(
            InvoiceSavedAfterObserver::class,
            [
                'config' => $this->configMock,
                'countryGuard' => $this->countryGuardMock,
                'taxInvoice' => $this->taxInvoiceMock,
                'messageManager' => $this->managerInterfaceMock,
                'invoiceSentRegistry' => $this->invoiceSentRegistryMock,
                'configValidator' => $this->configValidatorMock
            ]
        );
    }

    public function testNonDuplicativeInvoiceSentState()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Event $eventMock */
        $eventMock = $this->createPartialMock(Event::class, ['getInvoice']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Event\Observer $observerMock */
        $observerMock = $this->createPartialMock(Event\Observer::class, ['getEvent']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Invoice $invoiceMock */
        $invoiceMock = $this->createPartialMock(
            Invoice::class,
            [
                'getStore',
                'getOrder',
                'save'
            ]
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject|Order $orderMock */
        $orderMock = $this->createMock(Order::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Store $storeMock */
        $storeMock = $this->createMock(Store::class);

        $request = new Request();

        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $eventMock->expects($this->exactly(2))
            ->method('getInvoice')
            ->willReturn($invoiceMock);

        $invoiceMock->expects($this->exactly(3))
            ->method('getOrder')
            ->willReturn($orderMock);

        $this->invoiceSentRegistryMock->expects($this->exactly(2))
            ->method('hasInvoiceBeenSentToVertex')
            ->with($invoiceMock)
            ->will($this->onConsecutiveCalls(false, true));

        $invoiceMock->expects($this->exactly(4))
            ->method('getStore')
            ->willReturn($storeMock);

        $this->configMock->expects($this->exactly(2))
            ->method('isVertexActive')
            ->with($storeMock)
            ->willReturn(true);

        $this->configMock->expects($this->exactly(2))
            ->method('requestByInvoiceCreation')
            ->with($storeMock)
            ->willReturn(true);

        $this->countryGuardMock->expects($this->exactly(2))
            ->method('isOrderServiceableByVertex')
            ->with($orderMock)
            ->willReturn(true);

        $this->taxInvoiceMock->expects($this->once())
            ->method('prepareInvoiceData')
            ->with($invoiceMock, 'invoice')
            ->willReturn($request);

        $this->taxInvoiceMock->expects($this->once())
            ->method('sendInvoiceRequest')
            ->with($request, $orderMock)
            ->willReturn(true);

        $this->invoiceSentRegistryMock->expects($this->once())
            ->method('setInvoiceHasBeenSentToVertex')
            ->with($invoiceMock)
            ->willReturn($invoiceMock);

        $this->managerInterfaceMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with('The Vertex invoice has been sent.')
            ->willReturn($this->managerInterfaceMock);

        $this->invoiceSavedAfterObserverMock->execute($observerMock);
        $this->invoiceSavedAfterObserverMock->execute($observerMock);
    }

    public function testSendOrderAfterInvoiceSaveRequest()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Event $eventMock */
        $eventMock = $this->createPartialMock(Event::class, ['getInvoice']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Event\Observer $observerMock */
        $observerMock = $this->createPartialMock(Event\Observer::class, ['getEvent']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Invoice $invoiceMock */
        $invoiceMock = $this->createPartialMock(
            Invoice::class,
            [
                'getStore',
                'getOrder',
                'save'
            ]
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject|Order $orderMock */
        $orderMock = $this->createMock(Order::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Store $storeMock */
        $storeMock = $this->createMock(Store::class);

        $request = new Request();

        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $eventMock->expects($this->once())
            ->method('getInvoice')
            ->willReturn($invoiceMock);

        $invoiceMock->expects($this->exactly(2))
            ->method('getOrder')
            ->willReturn($orderMock);

        $this->invoiceSentRegistryMock->expects($this->once())
            ->method('hasInvoiceBeenSentToVertex')
            ->with($invoiceMock)
            ->willReturn(false);

        $invoiceMock->expects($this->exactly(2))
            ->method('getStore')
            ->willReturn($storeMock);

        $this->configMock->expects($this->once())
            ->method('isVertexActive')
            ->with($storeMock)
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('requestByInvoiceCreation')
            ->with($storeMock)
            ->willReturn(true);

        $this->countryGuardMock->expects($this->once())
            ->method('isOrderServiceableByVertex')
            ->with($orderMock)
            ->willReturn(true);

        $this->taxInvoiceMock->expects($this->once())
            ->method('prepareInvoiceData')
            ->with($invoiceMock, 'invoice')
            ->willReturn($request);

        $this->taxInvoiceMock->expects($this->once())
            ->method('sendInvoiceRequest')
            ->with($request, $orderMock)
            ->willReturn(true);

        $this->invoiceSentRegistryMock->expects($this->once())
            ->method('setInvoiceHasBeenSentToVertex')
            ->with($invoiceMock)
            ->willReturn($invoiceMock);

        $this->managerInterfaceMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with('The Vertex invoice has been sent.')
            ->willReturn($this->managerInterfaceMock);

        $this->invoiceSavedAfterObserverMock->execute($observerMock);
    }
}
