<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Plugin;

use Magento\Quote\Api\Data\AddressInterface;
use Vertex\Tax\Exception\ApiRequestException;
use Vertex\Tax\Model\Request\Address;
use Vertex\Tax\Model\Request\Type\QuotationRequest;
use Vertex\Tax\Model\TaxArea\TaxAreaRequest;
use Vertex\Tax\Model\TaxQuote\TaxQuoteRequest;
use Vertex\Tax\Model\TaxQuote\TaxQuoteResponse;
use Vertex\Tax\Model\Calculator;
use Vertex\Tax\Test\Unit\TestCase;
use Magento\Framework\DataObject;

/**
 * Test Vertex tax calculator functions.
 */
class CalculatorTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|Address */
    private $addressFormatterMock;

    /** @var Calculator */
    private $calculator;

    /** @var \PHPUnit_Framework_MockObject_MockObject|QuotationRequest */
    private $quotationRequestFormatterMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxAreaRequest */
    private $taxAreaRequestMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxQuoteRequest */
    private $taxQuoteRequestMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxQuoteResponse */
    private $taxQuoteResponseMock;

    /**
     * Perform test setup.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->addressFormatterMock = $this->createMock(Address::class);
        $this->quotationRequestFormatterMock = $this->createMock(QuotationRequest::class);
        $this->taxAreaRequestMock = $this->createMock(TaxAreaRequest::class);
        $this->taxQuoteRequestMock = $this->createMock(TaxQuoteRequest::class);
        $this->taxQuoteResponseMock = $this->createMock(TaxQuoteResponse::class);
        $this->calculator = $this->getObject(
            Calculator::class,
            [
                'addressFormatter' => $this->addressFormatterMock,
                'quotationRequestFormatter' => $this->quotationRequestFormatterMock,
                'taxAreaRequest' => $this->taxAreaRequestMock,
                'taxQuoteRequest' => $this->taxQuoteRequestMock,
                'taxQuoteResponse' => $this->taxQuoteResponseMock,
            ]
        );
    }

    /**
     * Test that a tax request may be permitted by a given country.
     *
     * @covers \Vertex\Tax\Model\Calculator::calculateTaxAreaIds()
     */
    public function testAcceptableCountryForTaxAreaCalculation()
    {
        $addressMock = $this->createMock(AddressInterface::class);
        $responseMock = $this->createPartialMock(
            DataObject::class,
            ['getTaxAreaWithHighestConfidence']
        );

        $this->addressFormatterMock->method('getFormattedAddressData')
            ->willReturn(['Country' => 'USA']);

        $this->taxAreaRequestMock->expects($this->once())
            ->method('taxAreaLookup')
            ->willReturn($responseMock);

        $this->calculator->calculateTaxAreaIds($addressMock, null);
    }

    /**
     * Test that a tax request may not be permitted by a given country.
     *
     * @covers \Vertex\Tax\Model\Calculator::calculateTaxAreaIds()
     */
    public function testUnacceptableCountryForTaxAreaCalculation()
    {
        $addressMock = $this->createMock(AddressInterface::class);

        $this->addressFormatterMock->method('getFormattedAddressData')
            ->willReturn(['Country' => 'CAN']);

        $this->taxAreaRequestMock->expects($this->never())
            ->method('taxAreaLookup');

        $this->assertTrue(
            $this->calculator->calculateTaxAreaIds($addressMock, null)
        );
    }

    /**
     * Test that a tax area request does not succeed.
     *
     * @covers \Vertex\Tax\Model\Calculator::calculateTaxAreaIds()
     */
    public function testInvalidApiResponseForTaxAreaCalculation()
    {
        $addressMock = $this->createMock(AddressInterface::class);

        $this->addressFormatterMock->method('getFormattedAddressData')
            ->willReturn(['Country' => 'USA']);

        $this->taxAreaRequestMock->expects($this->once())
            ->method('taxAreaLookup')
            ->willThrowException(new ApiRequestException(__('Unit Test')));

        $this->assertFalse(
            $this->calculator->calculateTaxAreaIds($addressMock, null)
        );
    }

    /**
     * Test that a tax area request succeeds with the area of highest confidence.
     *
     * Does not test for integrity of returned data, but only the return type.
     *
     * @covers \Vertex\Tax\Model\Calculator::calculateTaxAreaIds()
     */
    public function testHighestConfidenceTaxAreaResolution()
    {
        $responseMock = $this->createPartialMock(
            DataObject::class,
            ['getTaxAreaWithHighestConfidence']
        );
        $expectedResult = $this->createMock(DataObject::class);

        $responseMock->method('getTaxAreaWithHighestConfidence')
            ->willReturn($expectedResult);

        $this->taxAreaRequestMock->method('taxAreaLookup')
            ->willReturn($responseMock);

        $addressMock = $this->createMock(AddressInterface::class);

        $this->addressFormatterMock->method('getFormattedAddressData')
            ->willReturn(['Country' => 'USA']);

        $actualResult = $this->calculator->calculateTaxAreaIds($addressMock, null);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * Test that a tax quote request does not succeed.
     *
     * @covers \Vertex\Tax\Model\Calculator::calculateTax()
     */
    public function testInvalidTaxQuoteResponse()
    {
        $addressMock = $this->createMock(AddressInterface::class);

        $this->quotationRequestFormatterMock->method('getFormattedRequestData')
            ->willReturn([]);

        $this->taxQuoteRequestMock->method('taxQuote')
            ->willReturn(false);

        $this->assertFalse(
            $this->calculator->calculateTax($addressMock)
        );
    }

    /**
     * Test that a tax quote request succeeds.
     *
     * Does not test for integrity of returned data, but only the return type.
     *
     * @covers \Vertex\Tax\Model\Calculator::calculateTax()
     */
    public function testValidTaxQuoteResponse()
    {
        $addressMock = $this->createMock(AddressInterface::class);
        $requestMock = [null];
        $responseMock = [null];

        $this->quotationRequestFormatterMock->method('getFormattedRequestData')
            ->willReturn($requestMock);

        $this->taxQuoteRequestMock->method('taxQuote')
            ->with($requestMock)
            ->willReturn($responseMock);

        $this->taxQuoteResponseMock->expects($this->once())
            ->method('parseResponse')
            ->with($responseMock)
            ->willReturn($this->taxQuoteResponseMock);

        $this->assertInstanceOf(
            TaxQuoteResponse::class,
            $this->calculator->calculateTax($addressMock)
        );
    }
}
