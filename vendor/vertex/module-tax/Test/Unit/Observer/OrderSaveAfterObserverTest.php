<?php

namespace Vertex\Tax\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Store;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\TaxInvoice;
use Vertex\Tax\Observer\OrderSavedAfterObserver;
use Vertex\Tax\Test\Unit\TestCase;

class OrderSaveAfterObserverTest extends TestCase
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSendInvoiceAfterOrderSaveRequest()
    {
        $configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['isVertexActive', 'requestByOrderStatus', 'invoiceOrderStatus'])
            ->disableOriginalConstructor()
            ->getMock();

        $countryGuardMock = $this->getMockBuilder(CountryGuard::class)
            ->setMethods(['isOrderServiceableByVertex'])
            ->disableOriginalConstructor()
            ->getMock();

        $taxInvoiceMock = $this->getMockBuilder(TaxInvoice::class)
            ->setMethods(['prepareInvoiceData', 'sendInvoiceRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $managerInterfaceMock = $this->getMockBuilder(ManagerInterface::class)
            ->setMethods(['addSuccessMessage'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $orderSavedAfterObserverMock = $this->getObject(
            OrderSavedAfterObserver::class,
            [
                'config' => $configMock,
                'countryGuard' => $countryGuardMock,
                'taxInvoice' => $taxInvoiceMock,
                'messageManager' => $managerInterfaceMock
            ]
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject|Event $eventMock */
        $eventMock = $this->createPartialMock(Event::class, ['getOrder']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Event\Observer $observerMock */
        $observerMock = $this->createPartialMock(Event\Observer::class, ['getEvent']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Order $orderMock */
        $orderMock = $this->createMock(Order::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Store $storeMock */
        $storeMock = $this->createMock(Store::class);

        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $eventMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $orderMock->expects($this->exactly(2))
            ->method('getStore')
            ->willReturn($storeMock);

        $status = uniqid('order-status-');
        $orderMock->expects($this->exactly(1))
            ->method('getStatus')
            ->willReturn($status);

        $configMock->expects($this->once())
            ->method('isVertexActive')
            ->with($storeMock)
            ->willReturn(true);

        $configMock->expects($this->once())
            ->method('requestByOrderStatus')
            ->with($storeMock)
            ->willReturn(true);

        $configMock->expects($this->once())
            ->method('invoiceOrderStatus')
            ->with($storeMock)
            ->willReturn($status);

        $countryGuardMock->expects($this->once())
            ->method('isOrderServiceableByVertex')
            ->with($orderMock)
            ->willReturn(true);

        $taxInvoiceMock->expects($this->once())
            ->method('prepareInvoiceData')
            ->with($orderMock)
            ->willReturn([]);

        $taxInvoiceMock->expects($this->once())
            ->method('sendInvoiceRequest')
            ->with([], $orderMock)
            ->willReturn(true);

        $managerInterfaceMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with('The Vertex invoice has been sent.')
            ->willReturn($managerInterfaceMock);

        $orderSavedAfterObserverMock->execute($observerMock);
    }
}
