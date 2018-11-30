<?php

namespace Vertex\Tax\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Store\Model\Store;
use Vertex\Services\Invoice\Request;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ConfigurationValidator;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\TaxInvoice;
use Vertex\Tax\Observer\CreditMemoObserver;
use Vertex\Tax\Test\Unit\TestCase;

class CreditMemoObserverTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|Config */
    private $configMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ConfigurationValidator */
    private $configValidatorMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CountryGuard */
    private $countryGuardMock;

    /** @var CreditMemoObserver */
    private $creditMemoObserverMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ManagerInterface */
    private $managerInterfaceMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxInvoice */
    private $taxInvoiceMock;

    protected function setUp()
    {
        parent::setUp();

        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['isVertexActive'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->countryGuardMock = $this->getMockBuilder(CountryGuard::class)
            ->setMethods(['isOrderServiceableByVertex'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->taxInvoiceMock = $this->getMockBuilder(TaxInvoice::class)
            ->setMethods(['prepareInvoiceData', 'sendRefundRequest'])
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

        $this->creditMemoObserverMock = $this->getObject(
            CreditMemoObserver::class,
            [
                'config' => $this->configMock,
                'countryGuard' => $this->countryGuardMock,
                'taxInvoice' => $this->taxInvoiceMock,
                'messageManager' => $this->managerInterfaceMock,
                'configValidator' => $this->configValidatorMock,
            ]
        );
    }

    public function testSendRefundRequest()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Event $eventMock */
        $eventMock = $this->createPartialMock(Event::class, ['getCreditmemo']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Observer $observerMock */
        $observerMock = $this->createPartialMock(Observer::class, ['getEvent']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Creditmemo $creditMemoMock */
        $creditMemoMock = $this->createMock(Creditmemo::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Order $orderMock */
        $orderMock = $this->createMock(Order::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Store $storeMock */
        $storeMock = $this->createMock(Store::class);

        $request = new Request();

        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $eventMock->expects($this->once())
            ->method('getCreditmemo')
            ->willReturn($creditMemoMock);

        $creditMemoMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $orderMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->configMock->expects($this->once())
            ->method('isVertexActive')
            ->with($storeMock)
            ->willReturn(true);

        $this->countryGuardMock->expects($this->once())
            ->method('isOrderServiceableByVertex')
            ->with($orderMock)
            ->willReturn(true);

        $this->taxInvoiceMock->expects($this->once())
            ->method('prepareInvoiceData')
            ->with($creditMemoMock, 'refund')
            ->willReturn($request);

        $this->taxInvoiceMock->expects($this->once())
            ->method('sendRefundRequest')
            ->with($request, $orderMock)
            ->willReturn(true);

        $this->managerInterfaceMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with('The Vertex invoice has been refunded.')
            ->willReturn($this->managerInterfaceMock);

        $this->creditMemoObserverMock->execute($observerMock);
    }
}
