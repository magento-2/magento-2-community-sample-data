<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\TaxQuote;

use Vertex\Data\ConfigurationInterface;
use Vertex\Services\Quote\Request;
use Vertex\Services\Quote\Response;
use Vertex\Services\Quote\ResponseInterface;
use Vertex\Tax\Api\QuoteInterface;
use Vertex\Tax\Model\Api\ConfigBuilder;
use Vertex\Tax\Model\Api\Utility\MapperFactoryProxy;
use Vertex\Tax\Model\TaxQuote\CacheKeyGenerator;
use Vertex\Tax\Model\TaxQuote\TaxQuoteRequest;
use Vertex\Tax\Model\TaxRegistry;
use Vertex\Tax\Test\Unit\TestCase;
use Vertex\Utility\VersionDeterminer;

/**
 * Test tax quote request functionality.
 */
class TaxQuoteRequestTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|QuoteInterface */
    private $quoteMock;

    /** @var TaxQuoteRequest */
    private $taxQuoteRequest;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxRegistry */
    private $taxRegistryMock;

    /**
     * Perform test setup.
     */
    public function setUp()
    {
        parent::setUp();

        $versionDeterminerMock = $this->createMock(VersionDeterminer::class);
        $versionDeterminerMock->method('execute')
            ->willReturn('60');

        $configBuilderMock = $this->createMock(ConfigBuilder::class);
        $configBuilderMock->method('setStoreCode')
            ->willReturnSelf();
        $configBuilderMock->method('build')
            ->willReturn($this->createMock(ConfigurationInterface::class));

        $realMapperFactory = new \Vertex\Mapper\MapperFactory();

        $mapperFactory = $this->getObject(
            MapperFactoryProxy::class,
            [
                'versionDeterminer' => $versionDeterminerMock,
                'configBuilder' => $configBuilderMock,
                'factory' => $realMapperFactory,
            ]
        );

        $this->quoteMock = $this->createMock(QuoteInterface::class);
        $this->taxRegistryMock = $this->createMock(TaxRegistry::class);
        $this->taxQuoteRequest = $this->getObject(
            TaxQuoteRequest::class,
            [
                'quote' => $this->quoteMock,
                'cacheKeyGenerator' => $this->getObject(
                    CacheKeyGenerator::class,
                    ['mapperFactory' => $mapperFactory]
                ),
                'taxRegistry' => $this->taxRegistryMock,
                'mapperFactory' => $mapperFactory
            ]
        );
    }

    /**
     * Test cached tax response handling.
     *
     * @covers \Vertex\Tax\Model\TaxQuote\TaxQuoteRequest::taxQuote()
     */
    public function testTaxQuoteRequestCachedResponse()
    {
        $request = new Request();
        $response = new Response();

        $this->quoteMock->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $rawResponse = new \stdClass();
        $rawResponse->QuotationResponse = new \stdClass();

        $this->taxRegistryMock->expects($this->exactly(2))
            ->method('lookup')
            ->willReturnOnConsecutiveCalls(null, $rawResponse);

        // Vertex sendApiRequest should be called once, testing uncached
        $result = $this->taxQuoteRequest->taxQuote($request);
        $this->assertEquals($response, $result);

        // It should not be called again, testing cached
        $result = $this->taxQuoteRequest->taxQuote($request);
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
