<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ModuleManager;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;
use Vertex\Tax\Model\Request\Customer;
use Vertex\Tax\Model\RequestItem;
use Vertex\Tax\Model\RequestItemFactory;
use Vertex\Tax\Model\TaxInvoice;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TaxInvoiceTest extends TestCase
{
    /**
     * @covers \Vertex\Tax\Model\TaxInvoice::__construct()
     */
    public function testConstructThrowsNoError()
    {
        $this->getObject(TaxInvoice::class);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice::sendInvoiceRequest()
     */
    public function testSendInvoiceRequest()
    {
        // Attempts to save order status via repository
        $statusRepository = $this->createMock(OrderStatusHistoryRepositoryInterface::class);
        $statusRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function ($object) {
                        return $object->getComment() == 'Vertex Invoice sent successfully. Amount: $5.00';
                    }
                )
            );

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('addStatusHistoryComment')
            ->with('Vertex Invoice sent successfully. Amount: $5.00', false)
            ->willReturnCallback(
                function ($comment) {
                    $history = $this->createMock(OrderStatusHistoryInterface::class);
                    $history->method('getComment')
                        ->willReturn($comment);

                    return $history;
                }
            );

        $vertexMock = $this->createMock(ApiClient::class);
        $vertexMock->expects($this->once())
            ->method('sendApiRequest')
            ->with($this->anything(), 'invoice', $orderMock)
            ->willReturn(['TotalTax' => 5]);

        $taxInvoice = $this->getObject(
            TaxInvoice::class,
            [
                'vertex' => $vertexMock,
                'orderStatusRepository' => $statusRepository,
            ]
        );

        $taxInvoice->sendInvoiceRequest([], $orderMock);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice::sendInvoiceRequest()
     */
    public function testSendInvoiceRequestWhenTotalTaxIsArray()
    {
        $vertexMock = $this->createMock(ApiClient::class);
        $vertexMock->expects($this->once())
            ->method('sendApiRequest')
            ->willReturn(['TotalTax' => ['_' => 5]]);

        // Attempts to save order status via repository
        $statusRepository = $this->createMock(OrderStatusHistoryRepositoryInterface::class);
        $statusRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function ($object) {
                        return $object->getComment() == 'Vertex Invoice sent successfully. Amount: $5.00';
                    }
                )
            );

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('addStatusHistoryComment')
            ->with('Vertex Invoice sent successfully. Amount: $5.00', false)
            ->willReturnCallback(
                function ($comment) {
                    $history = $this->createMock(OrderStatusHistoryInterface::class);
                    $history->method('getComment')
                        ->willReturn($comment);

                    return $history;
                }
            );

        $taxInvoice = $this->getObject(
            TaxInvoice::class,
            [
                'vertex' => $vertexMock,
                'orderStatusRepository' => $statusRepository,
            ]
        );

        $taxInvoice->sendInvoiceRequest([], $orderMock);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice::sendInvoiceRequest()
     */
    public function testSendInvoiceRequestFailure()
    {
        $vertexMock = $this->createMock(ApiClient::class);
        $vertexMock->method('sendApiRequest')
            ->willReturn(false);

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())
            ->method('addErrorMessage');

        $orderMock = $this->createMock(Order::class);

        $invoice = $this->getObject(
            TaxInvoice::class,
            [
                'vertex' => $vertexMock,
                'messageManager' => $messageManager,
            ]
        );

        $invoice->sendInvoiceRequest([], $orderMock);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice::sendInvoiceRequest()
     */
    public function testSendInvoiceHistoryFailure()
    {
        $testException = new \Exception('Test Exception');

        $orderStatusRepository = $this->createMock(OrderStatusHistoryRepositoryInterface::class);
        $orderStatusRepository->method('save')
            ->willThrowException($testException);

        $order = $this->createMock(Order::class);
        $order->method('addStatusHistoryComment')
            ->willReturnCallback(
                function () {
                    return $this->createMock(OrderStatusHistoryInterface::class);
                }
            );

        $invoice = $this->getObject(
            TaxInvoice::class,
            [
                'orderStatusRepository' => $orderStatusRepository,
            ]
        );

        $invoice->sendInvoiceRequest([], $order);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice::sendRefundRequest()
     */
    public function testSendRefundRequest()
    {
        // Attempts to save order status via repository
        $statusRepository = $this->createMock(OrderStatusHistoryRepositoryInterface::class);
        $statusRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function ($object) {
                        return $object->getComment() == 'Vertex Invoice refunded successfully. Amount: $-5.00';
                    }
                )
            );

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('addStatusHistoryComment')
            ->with('Vertex Invoice refunded successfully. Amount: $-5.00', false)
            ->willReturnCallback(
                function ($comment) {
                    $history = $this->createMock(OrderStatusHistoryInterface::class);
                    $history->method('getComment')
                        ->willReturn($comment);

                    return $history;
                }
            );

        $vertexMock = $this->createMock(ApiClient::class);
        $vertexMock->expects($this->once())
            ->method('sendApiRequest')
            ->with($this->anything(), 'invoice_refund', $orderMock)
            ->willReturn(['TotalTax' => -5]);

        $taxInvoice = $this->getObject(
            TaxInvoice::class,
            [
                'vertex' => $vertexMock,
                'orderStatusRepository' => $statusRepository,
            ]
        );

        $taxInvoice->sendRefundRequest([], $orderMock);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice::sendRefundRequest()
     */
    public function testSendRefundRequestWhenTotalTaxIsArray()
    {
        $vertexMock = $this->createMock(ApiClient::class);
        $vertexMock->expects($this->once())
            ->method('sendApiRequest')
            ->willReturn(['TotalTax' => ['_' => -5]]);

        // Attempts to save order status via repository
        $statusRepository = $this->createMock(OrderStatusHistoryRepositoryInterface::class);
        $statusRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    function ($object) {
                        return $object->getComment() == 'Vertex Invoice refunded successfully. Amount: $-5.00';
                    }
                )
            );

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('addStatusHistoryComment')
            ->with('Vertex Invoice refunded successfully. Amount: $-5.00', false)
            ->willReturnCallback(
                function ($comment) {
                    $history = $this->createMock(OrderStatusHistoryInterface::class);
                    $history->method('getComment')
                        ->willReturn($comment);

                    return $history;
                }
            );

        $taxInvoice = $this->getObject(
            TaxInvoice::class,
            [
                'vertex' => $vertexMock,
                'orderStatusRepository' => $statusRepository,
            ]
        );

        $taxInvoice->sendRefundRequest([], $orderMock);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice::sendRefundRequest()
     */
    public function testSendRefundRequestFailure()
    {
        $vertexMock = $this->createMock(ApiClient::class);
        $vertexMock->method('sendApiRequest')
            ->willReturn(false);

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())
            ->method('addErrorMessage');

        $invoice = $this->getObject(
            TaxInvoice::class,
            [
                'vertex' => $vertexMock,
                'messageManager' => $messageManager,
            ]
        );

        $orderMock = $this->createMock(Order::class);

        $invoice->sendRefundRequest([], $orderMock);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice::sendRefundRequest()
     */
    public function testSendRefundHistoryFailure()
    {
        $testException = new \Exception('Test Exception');

        $orderStatusRepository = $this->createMock(OrderStatusHistoryRepositoryInterface::class);
        $orderStatusRepository->method('save')
            ->willThrowException($testException);

        $order = $this->createMock(Order::class);
        $order->method('addStatusHistoryComment')
            ->willReturnCallback(
                function () {
                    return $this->createMock(OrderStatusHistoryInterface::class);
                }
            );

        $invoice = $this->getObject(
            TaxInvoice::class,
            [
                'orderStatusRepository' => $orderStatusRepository,
            ]
        );

        $invoice->sendRefundRequest([], $order);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testPrepareInvoiceData()
    {
        $baseFormatter = $this->createMock(TaxInvoice\BaseFormatter::class);
        $baseFormatter->method('addFormattedSellerData')
            ->willReturn(
                [
                    'location_code' => '',
                    'transaction_type' => '',
                    'company_id' => '',
                    'company_street_1' => '',
                    'company_street_2' => '',
                    'company_city' => '',
                    'company_state' => '',
                    'company_postcode' => '',
                    'company_country' => '',
                    'trusted_id' => '',
                ]
            );
        $baseFormatter->method('addFormattedAddressData')
            ->willReturn(
                [
                    'customer_street1' => '',
                    'customer_street2' => '',
                    'customer_city' => '',
                    'customer_region' => '',
                    'customer_postcode' => '',
                    'customer_country' => '',
                    'tax_area_id' => '',
                ]
            );
        $baseFormatter->method('addRefundAdjustments')
            ->willReturnArgument(0);
        $baseFormatter->method('getFormattedShippingData')
            ->willReturn('shipping_info');
        $baseFormatter->method('getFormattedOrderGiftWrap')
            ->willReturn('order_gift_wrap');
        $baseFormatter->method('getFormattedOrderPrintCard')
            ->willReturn('order_print_card');

        $moduleManagerMock = $this->createMock(ModuleManager::class);
        $moduleManagerMock->method('isEnabled')
            ->with('Magento_GiftWrapping')
            ->willReturn(true);

        $itemFormatter = $this->getMockBuilder(TaxInvoice\ItemFormatter::class)
            ->setConstructorArgs(
                [
                    $this->createMock(Config::class),
                    $moduleManagerMock,
                    $this->createMock(TaxClassNameRepository::class),
                ]
            )
            ->setMethods(['getPreparedItemGiftWrap', 'getPreparedItemData'])
            ->getMock();
        $itemFormatter->method('getPreparedItemGiftWrap')
            ->willReturnCallback(
                function ($item) {
                    return 'gw-item-' . $item->getId();
                }
            );
        $itemFormatter->method('getPreparedItemData')
            ->willReturnCallback(
                function ($item) {
                    return 'item-' . $item->getId();
                }
            );

        $orderItemMock = $this->createMock(Order\Item::class);
        $orderItemMock->method('getIsVirtual')
            ->willReturn(false);
        $orderItemMock->method('getId')
            ->willReturn(1);
        $orderItemMock->method('getGwId')
            ->willReturn('gw1');
        $invoiceItemMock = $this->createMock(Order\Invoice\Item::class);
        $invoiceItemMock->method('getOrderItem')
            ->willReturn($orderItemMock);

        $parentItemMock = $this->createPartialMock(
            Order\Item::class,
            ['getHasChildren', 'getChildrenItems', 'getProduct', 'getId', 'getGwId']
        );
        $parentItemMock->method('getHasChildren')
            ->willReturn(true);
        $parentItemMock->method('getGwId')
            ->willReturn('gw2');
        $parentItemMock->method('getId')
            ->willReturn(2);

        $parentProductMock = $this->createPartialMock(Product::class, ['getPriceType']);
        $parentProductMock->method('getPriceType')
            ->willReturn(Product\Type\AbstractType::CALCULATE_CHILD);
        $parentItemMock->method('getProduct')
            ->willReturn($parentProductMock);

        $orderItemWithParentMock = $this->createMock(Order\Item::class);
        $orderItemWithParentMock->method('getParentItem')
            ->willReturn($parentItemMock);
        $orderItemWithParentMock->method('getId')
            ->willReturn(3);
        $parentItemMock->method('getChildrenItems')
            ->willReturn([$orderItemWithParentMock]);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getIncrementId')
            ->willReturn('001');
        $orderMock->method('getCreatedAt')
            ->willReturn('2017-01-01');
        $orderMock->expects($this->atLeastOnce())
            ->method('getShippingAddress');
        $orderMock->expects($this->never())
            ->method('getBillingAddress');

        $fakeItemMock = $this->createPartialMock(DataObject::class, ['getParentItem']);
        $fakeItemMock->method('getParentItem')
            ->willReturn(null);

        $invoiceMock = $this->createMock(Order\Creditmemo::class);
        $invoiceMock->method('getOrder')
            ->willReturn($orderMock);
        $invoiceMock->method('getAllItems')
            ->willReturn(
                [
                    $invoiceItemMock, // Tests that order item is pulled from invoice item
                    $parentItemMock, // Tests that child item is pulled from parent item
                    $orderItemWithParentMock, // Tests that child item is not calculated by itself
                    $fakeItemMock, // Tests that non-items are not calculated
                ]
            );

        $customerFormatterMock = $this->createMock(Customer::class);
        $customerFormatterMock->method('taxClassNameByCustomerGroupId')
            ->willReturn('customer_class');
        $customerFormatterMock->method('getCustomerCodeById')
            ->willReturn('customer_code');

        $requestMock = $this->createMock(RequestItem::class);
        $requestMock->expects($this->once())
            ->method('setRequestType')
            ->with('InvoiceRequest')
            ->willReturnSelf();
        $requestMock->expects($this->once())
            ->method('setDocumentNumber')
            ->with('001')
            ->willReturnSelf();
        $requestMock->expects($this->once())
            ->method('setDocumentDate')
            ->with('2017-01-01')
            ->willReturnSelf();
        $requestMock->expects($this->once())
            ->method('setCustomerClass')
            ->with('customer_class')
            ->willReturnSelf();
        $requestMock->expects($this->once())
            ->method('setCustomerCode')
            ->with('customer_code')
            ->willReturnSelf();
        $requestMock->expects($this->once())
            ->method('setOrderItems')
            ->with(
                $this->callback(
                    function (array $info) {
                        $shouldBeInArray = [
                            'shipping_info',
                            'order_gift_wrap',
                            'order_print_card',
                            'item-1',
                            'gw-item-1',
                            'item-3',
                        ];
                        $shouldNotBeInArray = [
                            'item-2',
                            'gw-item-3',
                        ];
                        foreach ($shouldBeInArray as $inArrayTest) {
                            $this->assertContains($inArrayTest, $info);
                        }
                        foreach ($shouldNotBeInArray as $notInArrayTest) {
                            $this->assertNotContains($notInArrayTest, $info);
                        }
                        return true;
                    }
                )
            )
            ->willReturnSelf();

        $requestFactoryMock = $this->createMock(RequestItemFactory::class);
        $requestFactoryMock->method('create')
            ->willReturn($requestMock);

        $dateTimeMock = $this->createMock(DateTime::class);
        $dateTimeMock->method('timestamp')
            ->willReturnCallback(
                function ($input) {
                    return strtotime($input);
                }
            );
        $dateTimeMock->method('date')
            ->willReturnCallback(
                function ($format, $input = null) {
                    return date($format, $input);
                }
            );

        $invoice = $this->getObject(
            TaxInvoice::class,
            [
                'baseFormatter' => $baseFormatter,
                'itemFormatter' => $itemFormatter,
                'requestItemFactory' => $requestFactoryMock,
                'customerFormatter' => $customerFormatterMock,
                'moduleManager' => $moduleManagerMock,
                'dateTime' => $dateTimeMock
            ]
        );

        $invoice->prepareInvoiceData($invoiceMock);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice
     */
    public function testThatPrepareInvoiceDataUsesBillingAddressWhenOrderIsVirtual()
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getIsVirtual')
            ->willReturn(true);
        $orderMock->method('getAllItems')
            ->willReturn([]);

        $addressMock = $this->createMock(Order\Address::class);
        $addressMock->method('getOrder')
            ->willReturn($orderMock);

        $orderMock->expects($this->atLeastOnce())
            ->method('getBillingAddress')
            ->willReturn($addressMock);
        $orderMock->expects($this->never())
            ->method('getShippingAddress');

        $requestItemMock = $this->getObject(RequestItem::class);
        $requestItemFactoryMock = $this->createMock(RequestItemFactory::class);
        $requestItemFactoryMock->method('create')
            ->willReturn($requestItemMock);

        $countryInfoMock = $this->createMock(CountryInformationInterface::class);
        $countryInfoMock->method('getThreeLetterAbbreviation')
            ->willReturn('');

        $countryAcquirerMock = $this->createMock(CountryInformationAcquirerInterface::class);
        $countryAcquirerMock->method('getCountryInfo')
            ->willReturn($countryInfoMock);

        $baseFormatter = $this->getObject(
            TaxInvoice\BaseFormatter::class,
            ['countryInfoAcquirer' => $countryAcquirerMock]
        );
        $itemFormatter = $this->getObject(TaxInvoice\ItemFormatter::class);

        $invoice = $this->getObject(
            TaxInvoice::class,
            [
                'requestItemFactory' => $requestItemFactoryMock,
                'baseFormatter' => $baseFormatter,
                'itemFormatter' => $itemFormatter
            ]
        );
        $invoice->prepareInvoiceData($orderMock);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxInvoice
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testExceptionWhenPreparingInvoiceDataIsLogged()
    {
        $exception = new LocalizedException(__('Test Fail'));

        $baseFormatter = $this->createMock(TaxInvoice\BaseFormatter::class);
        $baseFormatter->method('addFormattedSellerData')
            ->willThrowException($exception);

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('critical')
            ->with($this->stringContains('Test Fail'));

        /** @var TaxInvoice $invoice */
        $invoice = $this->getObject(
            TaxInvoice::class,
            [
                'baseFormatter' => $baseFormatter,
                'logger' => $loggerMock
            ]
        );

        $invoice->prepareInvoiceData(null);
    }
}
