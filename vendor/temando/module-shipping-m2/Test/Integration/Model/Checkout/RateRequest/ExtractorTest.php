<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Checkout\RateRequest;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

class ExtractorTest extends \PHPUnit\Framework\TestCase
{
    /** @var ObjectManager $objectManager */
    private $objectManager;
    /** @var Extractor $extractor */
    private $extractor;
    /** @var RateRequest $rateRequest */
    private $rateRequest;
    /** @var RateRequest $invalidRateRequest */
    private $invalidRateRequest;
    /** @var Quote $testQuote */
    private $testQuote;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->extractor = $this->objectManager->create(Extractor::class);

        $this->testQuote = $this->objectManager->create(Quote::class);
        $this->testQuote->setId(99);
        $this->testQuote->setStoreId(99);

        $shippingAddress1 = $this->objectManager->create(Address::class);
        $shippingAddress1->setData('city', 'Leipzig');
        $shippingAddress1->setData('country_id', 'DE');
        $shippingAddress1->setData('postcode', '04229');
        $shippingAddress1->setData('region_id', '91');
        $shippingAddress1->setData('street', 'Nonnenstraße 11');
        $shippingAddress1->setData('region_code', 'SAS');
        $shippingAddress2 = $this->objectManager->create(Address::class);
        $shippingAddress2->setData('city', 'Dresden');
        $shippingAddress2->setData('country_id', 'DE');
        $shippingAddress2->setData('postcode', '010101');
        $shippingAddress2->setData('region_id', '11');
        $shippingAddress2->setData('street', 'Teststraße 11');
        $shippingAddress2->setData('region_code', 'SAS');

        $cartItem1 = $this->getMockBuilder(\Magento\Quote\Model\Quote\Item::class)->setMethods(['getAddress'])
                ->disableOriginalConstructor()->getMock();
        $cartItem1->method('getAddress')->willReturn($shippingAddress1);
        $cartItem2 = $this->getMockBuilder(\Magento\Quote\Model\Quote\Item::class)->setMethods(['getAddress'])
            ->disableOriginalConstructor()->getMock();
        $cartItem2->method('getAddress')->willReturn($shippingAddress2);
        $cartItem1->setQuote($this->testQuote);

        /** @var RateRequest rateRequest */
        $this->rateRequest = $this->objectManager->create(RateRequest::class);
        $this->rateRequest->setData('all_items', [
            '0' => $cartItem1,
            '1' => $cartItem2,
        ]);
        $this->rateRequest->setData('dest_country_id', 'DE');
        $this->rateRequest->setData('dest_region_id', '91');
        $this->rateRequest->setData('dest_region_code', 'SAS');
        $this->rateRequest->setData('dest_street', 'Nonnenstraße 11');
        $this->rateRequest->setData('dest_city', 'Leipzig');
        $this->rateRequest->setData('dest_postcode', '04229');

        $this->invalidRateRequest = $this->objectManager->create(RateRequest::class);
        $this->invalidRateRequest->setData('all_items', []);
        $this->invalidRateRequest->setData('dest_country_id', 'DE');
        $this->invalidRateRequest->setData('dest_region_id', '91');
        $this->invalidRateRequest->setData('dest_region_code', 'SAS');
        $this->invalidRateRequest->setData('dest_street', 'Nonnenstraße 11');
        $this->invalidRateRequest->setData('dest_city', 'Leipzig');
        $this->invalidRateRequest->setData('dest_postcode', '04229');
    }

    /**
     * @test
     */
    public function getQuoteTest()
    {
        $result = $this->extractor->getQuote($this->rateRequest);
        $this->assertEquals($this->testQuote, $result);
    }

    /**
     * @test
     */
    public function getShippingAddressTest()
    {
        $result = $this->extractor->getShippingAddress($this->rateRequest);

        $shippingAddress1 = $this->objectManager->create(Address::class);
        $shippingAddress1->setData('city', 'Leipzig');
        $shippingAddress1->setData('country_id', 'DE');
        $shippingAddress1->setData('postcode', '04229');
        $shippingAddress1->setData('region_id', '91');
        $shippingAddress1->setData('street', 'Nonnenstraße 11');
        $shippingAddress1->setData('region_code', 'SAS');

        $this->assertEquals($shippingAddress1, $result);
    }

    /**
     * @test
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage No items to ship found in rates request.
     */
    public function noItemsToShipTest()
    {
        $this->extractor->getShippingAddress($this->invalidRateRequest);
    }

    /**
     * @test
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage No items to ship found in rates request.
     */
    public function noItemsToShipTest2()
    {
        $this->extractor->getQuote($this->invalidRateRequest);
    }
}
