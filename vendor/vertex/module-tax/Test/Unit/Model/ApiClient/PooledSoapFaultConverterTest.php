<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\ApiClient;

use Vertex\Tax\Exception\ApiRequestException;
use Vertex\Tax\Model\ApiClient\PooledSoapFaultConverter;
use Vertex\Tax\Model\ApiClient\SoapFaultConverterInterface;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Tests the functionality of the Pooled Converter
 */
class PooledSoapFaultConverterTest extends TestCase
{
    /**
     * Test that non-SoapFaultConverterInterfaces cause errors when supplied as constructor parameters
     *
     * @return void
     */
    public function testExceptionThrownIfNonConverterProvidedInConstructor()
    {
        $nonConverter = new \stdClass();

        $this->expectException(\InvalidArgumentException::class);
        $this->getObject(PooledSoapFaultConverter::class, ['converters' => [$nonConverter]]);
    }

    /**
     * Test that SoapFaultConverterInterfaces do not cause errors when supplied as constructor parameters
     *
     * @return void
     */
    public function testNoExceptionThrownWhenConstructorGivenOnlyConverters()
    {
        $converter = $this->getMockBuilder(SoapFaultConverterInterface::class)
            ->getMockForAbstractClass();

        $this->getObject(PooledSoapFaultConverter::class, ['converters' => [$converter]]);
    }

    /**
     * Test that the pooled converter will call all ->convert methods if one doesn't return an Exception
     *
     * @return void
     */
    public function testAllConvertersCalledWhenAllReturnNull()
    {
        $amt = rand(5, 15);
        $converters = [];
        for ($i = 0; $i < $amt; ++$i) {
            $converter = $this->getMockBuilder(SoapFaultConverterInterface::class)
                ->getMockForAbstractClass();

            $converter->expects($this->once())
                ->method('convert')
                ->willReturn(null);

            $converters[] = $converter;
        }

        $fault = new \SoapFault('Server', 'Test');
        $pooledConverter = $this->getObject(PooledSoapFaultConverter::class, ['converters' => $converters]);
        $pooledConverter->convert($fault);
    }

    /**
     * Test that the pooled converter will stop calling convert methods if one returns an Exception
     *
     * @return void
     */
    public function testOnlyCallsUntilExceptionIsGiven()
    {
        $converter1 = $this->getMockBuilder(SoapFaultConverterInterface::class)
            ->getMockForAbstractClass();
        $converter1->expects($this->once())
            ->method('convert')
            ->willReturn(null);

        $converter2 = $this->getMockBuilder(SoapFaultConverterInterface::class)
            ->getMockForAbstractClass();
        $converter2->expects($this->once())
            ->method('convert')
            ->willReturn(new ApiRequestException(__('Test')));

        $converter3 = $this->getMockBuilder(SoapFaultConverterInterface::class)
            ->getMockForAbstractClass();
        $converter3->expects($this->never())
            ->method('convert');

        $fault = new \SoapFault('Server', 'Test');
        $pooledConverter = $this->getObject(
            PooledSoapFaultConverter::class,
            ['converters' => [$converter1, $converter2, $converter3]]
        );
        $pooledConverter->convert($fault);
    }
}
