<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Vertex;

use Magento\Framework\Exception\LocalizedException;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Test\Unit\TestCase;

class SendRequestTest extends TestCase
{
    const VERTEX_HOST = 'fake_vertex_host';
    const VERTEX_LOOKUP_HOST = 'fake_lookup_host';
    const CALCULATION_FUNCTION = 'calculate';
    const LOOKUP_FUNCTION = 'lookup';

    public function setUp()
    {
        parent::setUp();
    }

    private function createCalculationConfigMock()
    {
        return $this->createPartialMock(Config::class, ['getVertexHost', 'getCalculationFunction']);
    }

    private function createValidationConfigMock()
    {
        return $this->createPartialMock(
            Config::class,
            ['getVertexHost', 'getVertexAddressHost', 'getValidationFunction']
        );
    }

    public function testHappyTaxCalculation()
    {
        $calculationReturn = ['moo'];

        $configMock = $this->createCalculationConfigMock();
        $configMock->expects($this->atLeastOnce())
            ->method('getCalculationFunction')
            ->willReturn(static::CALCULATION_FUNCTION);

        $soapClientMock = $this->createPartialMock(\SoapClient::class, [static::CALCULATION_FUNCTION]);
        $soapClientMock->expects($this->once())
            ->method(static::CALCULATION_FUNCTION)
            ->willReturn($calculationReturn);

        $vertex = $this->getObject(
            ApiClient::class,
            ['config' => $configMock]
        );
        $result = $this->invokeInaccessibleMethod($vertex, 'performSoapCall', $soapClientMock, 'quote', '');

        $this->assertEquals($calculationReturn, $result);
    }

    public function testHappyTaxAreaLookup()
    {
        $lookupReturn = ['cow'];

        $configMock = $this->createValidationConfigMock();
        $configMock->expects($this->atLeastOnce())
            ->method('getValidationFunction')
            ->willReturn(static::LOOKUP_FUNCTION);

        $soapClientMock = $this->createPartialMock(\SoapClient::class, [static::LOOKUP_FUNCTION]);
        $soapClientMock->expects($this->once())
            ->method(static::LOOKUP_FUNCTION)
            ->willReturn($lookupReturn);

        $vertex = $this->getObject(
            ApiClient::class,
            ['config' => $configMock]
        );
        $result = $this->invokeInaccessibleMethod($vertex, 'performSoapCall', $soapClientMock, 'tax_area_lookup', '');

        $this->assertEquals($lookupReturn, $result);
    }

    public function testExceptionWhenNoValidationFunction()
    {
        $this->expectException(LocalizedException::class);

        $configMock = $this->createValidationConfigMock();

        $soapMock = $this->createMock(\SoapClient::class);

        $vertex = $this->getObject(
            ApiClient::class,
            [
                'config' => $configMock,
            ]
        );
        $this->invokeInaccessibleMethod($vertex, 'performSoapCall', $soapMock, 'tax_area_lookup', '');
    }

    public function testExceptionWhenNoCalculationFunction()
    {
        $this->expectException(LocalizedException::class);

        $configMock = $this->createCalculationConfigMock();

        $soapMock = $this->createMock(\SoapClient::class);

        $vertex = $this->getObject(
            ApiClient::class,
            [
                'config' => $configMock,
            ]
        );
        $this->invokeInaccessibleMethod($vertex, 'performSoapCall', $soapMock, 'quote', '');
    }
}
