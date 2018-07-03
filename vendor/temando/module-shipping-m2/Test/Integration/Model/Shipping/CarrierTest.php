<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipping;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Item;
use Magento\TestFramework\Helper\Bootstrap;
use Psr\Log\LogLevel;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Model\Order\OrderReference;
use Temando\Shipping\Model\ResourceModel\Order\OrderRepository;
use Temando\Shipping\Model\ResourceModel\Shipment\ShipmentRepository;
use Temando\Shipping\Model\Shipment\TrackEventInterface;
use Temando\Shipping\Test\Integration\Provider\RateRequestProvider;
use Temando\Shipping\Webservice\Logger;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Shipping Carrier Test
 *
 * @magentoAppIsolation enabled
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Delegate provisioning of test data to separate class
     * @return RateRequest[][]
     */
    public function getRateRequest()
    {
        return RateRequestProvider::getRateRequest();
    }

    /**
     * Delegate provisioning of test data to separate class
     * @return RateRequest|OrderResponseTypeInterface[][]
     */
    public function getRateRequestWithShippingExperience()
    {
        return RateRequestProvider::getRateRequestWithShippingExperience();
    }

    /**
     * @test
     */
    public function carrierHasTrackingCapabilities()
    {
        /** @var Carrier $carrier */
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class);

        $this->assertTrue($carrier->isTrackingAvailable());
    }

    /**
     * @test
     */
    public function carrierMethodsAreEmpty()
    {
        /** @var Carrier $carrier */
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class);

        $this->assertInternalType('array', $carrier->getAllowedMethods());
        $this->assertEmpty($carrier->getAllowedMethods());
    }

    /**
     * @test
     * @dataProvider getRateRequest
     * @magentoConfigFixture default_store general/store_information/name Foo Store
     *
     * @param RateRequest $rateRequest
     */
    public function collectRatesRepositoryError(RateRequest $rateRequest)
    {
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->setMethods(['log'])
            ->disableOriginalConstructor()
            ->getMock();
        $loggerMock
            ->expects($this->once())
            ->method('log')
            ->with($this->equalTo(LogLevel::WARNING));

        $orderRepository = $this->getMockBuilder(OrderRepository::class)
            ->setMethods(['save'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderRepository
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new CouldNotSaveException(__('Foo')));

        /** @var Carrier $carrier */
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class, [
            'logger' => $loggerMock,
            'orderRepository' => $orderRepository,
        ]);

        // replace quote by mock
        /** @var Item $item */
        foreach ($rateRequest->getAllItems() as $item) {
            $product = Bootstrap::getObjectManager()->create(Product::class);
            $quoteData = $item->getQuote()->getData();
            /** @var Quote|\PHPUnit_Framework_MockObject_MockObject $quote */
            $quote = $this->getMockBuilder(Quote::class)
                ->setMethods(['getShippingAddress', 'getBillingAddress'])
                ->disableOriginalConstructor()
                ->getMock();
            $quote
                ->expects($this->any())
                ->method('getShippingAddress')
                ->willReturn($quoteData['shipping_address']);
            $quote
                ->expects($this->any())
                ->method('getBillingAddress')
                ->willReturn($quoteData['billing_address']);
            $quote->setData($quoteData);
            $item->setQuote($quote);
            $item->setData('product', $product);
        }

        $ratesResult = $carrier->collectRates($rateRequest);

        $this->assertTrue($ratesResult->getError());
        $rates = $ratesResult;
        foreach ($rates as $rate) {
            $this->assertInstanceOf(Error::class, $rate);
        }
    }

    /**
     * @test
     * @dataProvider getRateRequestWithShippingExperience
     * @magentoConfigFixture default_store general/store_information/name Foo Store
     *
     * @param RateRequest $rateRequest
     * @param OrderResponseTypeInterface $orderResponseType
     */
    public function collectRatesSuccess(RateRequest $rateRequest, OrderResponseTypeInterface $orderResponseType)
    {
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->setMethods(['log'])
            ->disableOriginalConstructor()
            ->getMock();
        $loggerMock
            ->expects($this->never())
            ->method('log')
            ->with($this->equalTo(LogLevel::WARNING));

        $orderRepository = $this->getMockBuilder(OrderRepository::class)
            ->setMethods(['save'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($orderResponseType);

        /** @var Carrier $carrier */
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class, [
            'logger' => $loggerMock,
            'orderRepository' => $orderRepository,
        ]);

        // replace quote by mock
        /** @var Item $item */
        foreach ($rateRequest->getAllItems() as $item) {
            $product = Bootstrap::getObjectManager()->create(Product::class);
            $quoteData = $item->getQuote()->getData();
            /** @var Quote|\PHPUnit_Framework_MockObject_MockObject $quote */
            $quote = $this->getMockBuilder(Quote::class)
                ->setMethods(['getShippingAddress', 'getBillingAddress'])
                ->disableOriginalConstructor()
                ->getMock();
            $quote
                ->expects($this->any())
                ->method('getShippingAddress')
                ->willReturn($quoteData['shipping_address']);
            $quote
                ->expects($this->any())
                ->method('getBillingAddress')
                ->willReturn($quoteData['billing_address']);
            $quote->setData($quoteData);
            $item->setQuote($quote);
            $item->setData('product', $product);
        }

        $rates = $carrier->collectRates($rateRequest)->getAllRates();
        $this->assertNotEmpty($rates);
        foreach ($rates as $rate) {
            $this->assertEquals(Carrier::CODE, $rate->getData('carrier'));
        }
    }

    /**
     * @test
     */
    public function trackingApiUnavailable()
    {
        $salesTrack = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order\Shipment\Track::class);

        $trackingNumber = '12-34-ABCD';
        $trackingUrl = 'https://example.com/track';
        $apiErrorMessage = 'unavailable foo';

        $shipmentReference = Bootstrap::getObjectManager()->create(ShipmentReferenceInterface::class, ['data' => [
            ShipmentReferenceInterface::ENTITY_ID => 42,
            ShipmentReferenceInterface::SHIPMENT_ID => 42,
            ShipmentReferenceInterface::EXT_LOCATION_ID => '1234-5678',
            ShipmentReferenceInterface::EXT_SHIPMENT_ID => '8765-4321',
            ShipmentReferenceInterface::EXT_TRACKING_REFERENCE => $trackingNumber,
            ShipmentReferenceInterface::EXT_TRACKING_URL => $trackingUrl,
        ]]);

        $messageManager = $this->getMockBuilder(\Magento\Framework\Message\Manager::class)
            ->setMethods(['addErrorMessage'])
            ->disableOriginalConstructor()
            ->getMock();
        $messageManager
            ->expects($this->once())
            ->method('addErrorMessage');

        $shipmentRepository = $this->getMockBuilder(ShipmentRepository::class)
            ->setMethods(['getTrackingByNumber', 'getReferenceByTrackingNumber', 'getShipmentTrack'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentRepository
            ->expects($this->once())
            ->method('getTrackingByNumber')
            ->willThrowException(new NoSuchEntityException(__($apiErrorMessage)));
        $shipmentRepository
            ->expects($this->once())
            ->method('getReferenceByTrackingNumber')
            ->willReturn($shipmentReference);
        $shipmentRepository
            ->expects($this->once())
            ->method('getShipmentTrack')
            ->willReturn($salesTrack);

        /** @var Carrier $carrier */
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class, [
            'messageManager' => $messageManager,
            'shipmentRepository' => $shipmentRepository,
        ]);

        /** @var \Magento\Shipping\Model\Tracking\Result\Status $trackingInfo */
        $trackingInfo = $carrier->getTrackingInfo($trackingNumber);
        $this->assertEquals($trackingNumber, $trackingInfo->getData('tracking'));
        $this->assertEquals($trackingUrl, $trackingInfo->getData('url'));
        $this->assertEmpty($trackingInfo->getData('progressdetail'));
    }

    /**
     * @test
     */
    public function shipmentReferenceNotFound()
    {
        $salesTrack = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order\Shipment\Track::class);

        $trackingNumber = '12-34-ABCD';
        $deliveryStatus = 'delivered';
        $repoExceptionMessage = 'foo does not exist';

        $trackEvent = Bootstrap::getObjectManager()->create(TrackEventInterface::class, ['data' => [
            TrackEventInterface::TRACKING_EVENT_ID => $trackingNumber,
            TrackEventInterface::OCCURRED_AT => '1999-01-19T03:03:33.000Z',
            TrackEventInterface::STATUS => $deliveryStatus,
        ]]);
        $trackEvents = [$trackEvent];

        $messageManager = $this->getMockBuilder(\Magento\Framework\Message\Manager::class)
            ->setMethods(['addErrorMessage'])
            ->disableOriginalConstructor()
            ->getMock();
        $messageManager
            ->expects($this->once())
            ->method('addErrorMessage');

        $shipmentRepository = $this->getMockBuilder(ShipmentRepository::class)
            ->setMethods(['getTrackingByNumber', 'getReferenceByTrackingNumber', 'getShipmentTrack'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentRepository
            ->expects($this->once())
            ->method('getTrackingByNumber')
            ->willReturn($trackEvents);
        $shipmentRepository
            ->expects($this->once())
            ->method('getReferenceByTrackingNumber')
            ->willThrowException(new NoSuchEntityException(__($repoExceptionMessage)));
        $shipmentRepository
            ->expects($this->once())
            ->method('getShipmentTrack')
            ->willReturn($salesTrack);

        /** @var Carrier $carrier */
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class, [
            'messageManager' => $messageManager,
            'shipmentRepository' => $shipmentRepository,
        ]);

        /** @var \Magento\Shipping\Model\Tracking\Result\Status $trackingInfo */
        $trackingInfo = $carrier->getTrackingInfo($trackingNumber);
        $this->assertEquals($trackingNumber, $trackingInfo->getData('tracking'));
        $this->assertEmpty($trackingInfo->getData('url'));
        $this->assertNotEmpty($trackingInfo->getData('progressdetail'));

        $progressDetail = $trackingInfo->getData('progressdetail');
        $this->assertInternalType('array', $progressDetail);
        $this->assertCount(1, $progressDetail);
        $this->assertEquals($deliveryStatus, $progressDetail[0]['activity']);
    }

    /**
     * @test
     */
    public function trackingInfoGatheredSuccessfully()
    {
        $salesTrack = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order\Shipment\Track::class);

        $trackingNumber = '12-34-ABCD';
        $trackingUrl = 'https://example.com/track';
        $deliveryStatus = 'delivered';

        $trackEvent = Bootstrap::getObjectManager()->create(TrackEventInterface::class, ['data' => [
            TrackEventInterface::TRACKING_EVENT_ID => $trackingNumber,
            TrackEventInterface::OCCURRED_AT => '1999-01-19T03:03:33.000Z',
            TrackEventInterface::STATUS => $deliveryStatus,
        ]]);
        $trackEvents = [$trackEvent];

        $shipmentReference = Bootstrap::getObjectManager()->create(ShipmentReferenceInterface::class, ['data' => [
            ShipmentReferenceInterface::ENTITY_ID => 42,
            ShipmentReferenceInterface::SHIPMENT_ID => 42,
            ShipmentReferenceInterface::EXT_LOCATION_ID => '1234-5678',
            ShipmentReferenceInterface::EXT_SHIPMENT_ID => '8765-4321',
            ShipmentReferenceInterface::EXT_TRACKING_REFERENCE => $trackingNumber,
            ShipmentReferenceInterface::EXT_TRACKING_URL => $trackingUrl,
        ]]);

        $messageManager = $this->getMockBuilder(\Magento\Framework\Message\Manager::class)
            ->setMethods(['addErrorMessage'])
            ->disableOriginalConstructor()
            ->getMock();
        $messageManager
            ->expects($this->never())
            ->method('addErrorMessage');

        $shipmentRepository = $this->getMockBuilder(ShipmentRepository::class)
            ->setMethods(['getTrackingByNumber', 'getReferenceByTrackingNumber', 'getShipmentTrack'])
            ->disableOriginalConstructor()
            ->getMock();
        $shipmentRepository
            ->expects($this->once())
            ->method('getTrackingByNumber')
            ->willReturn($trackEvents);
        $shipmentRepository
            ->expects($this->once())
            ->method('getReferenceByTrackingNumber')
            ->willReturn($shipmentReference);
        $shipmentRepository
            ->expects($this->once())
            ->method('getShipmentTrack')
            ->willReturn($salesTrack);

        /** @var Carrier $carrier */
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class, [
            'messageManager' => $messageManager,
            'shipmentRepository' => $shipmentRepository,
        ]);

        /** @var \Magento\Shipping\Model\Tracking\Result\Status $trackingInfo */
        $trackingInfo = $carrier->getTrackingInfo($trackingNumber);
        $this->assertEquals($trackingNumber, $trackingInfo->getData('tracking'));
        $this->assertEquals($trackingUrl, $trackingInfo->getData('url'));
        $this->assertNotEmpty($trackingInfo->getData('progressdetail'));

        $progressDetail = $trackingInfo->getData('progressdetail');
        $this->assertInternalType('array', $progressDetail);
        $this->assertCount(1, $progressDetail);
        $this->assertEquals($deliveryStatus, $progressDetail[0]['activity']);
    }
}
