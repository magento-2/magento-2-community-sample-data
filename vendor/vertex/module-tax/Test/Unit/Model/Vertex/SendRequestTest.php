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
    const CALCULATION_FUNCTION = 'CalculateTax60';
    const LOOKUP_FUNCTION = 'LookupTaxAreas60';

    public function setUp()
    {
        parent::setUp();
    }

    private function createCalculationConfigMock()
    {
        return $this->createPartialMock(Config::class, ['getVertexHost']);
    }

    private function createValidationConfigMock()
    {
        return $this->createPartialMock(
            Config::class,
            ['getVertexHost', 'getVertexAddressHost']
        );
    }

    public function testHappyTaxCalculation()
    {
        $calculationReturn = ['moo'];

        $configMock = $this->createCalculationConfigMock();

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
}
