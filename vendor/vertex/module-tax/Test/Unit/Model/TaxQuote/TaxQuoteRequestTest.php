<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\TaxQuote;

use Vertex\Tax\Model\TaxQuote\TaxQuoteRequest;
use Vertex\Tax\Model\TaxQuote\CacheKeyGenerator;
use Vertex\Tax\Model\TaxRegistry;
use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Test tax quote request functionality.
 */
class TaxQuoteRequestTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|ApiClient */
    private $apiClientMock;

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

        $this->apiClientMock = $this->createMock(ApiClient::class);
        $this->taxRegistryMock = $this->createMock(TaxRegistry::class);
        $this->taxQuoteRequest = $this->getObject(
            TaxQuoteRequest::class,
            [
                'vertex' => $this->apiClientMock,
                'cacheKeyGenerator' => $this->getObject(CacheKeyGenerator::class),
                'taxRegistry' => $this->taxRegistryMock,
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
        $this->apiClientMock->expects($this->once())
            ->method('sendApiRequest')
            ->willReturn('PI');

        $this->taxRegistryMock->expects($this->exactly(2))
            ->method('lookup')
            ->willReturnOnConsecutiveCalls(null, 'PI');

        // Vertex sendApiRequest should be called once, testing uncached
        $result = $this->taxQuoteRequest->taxQuote([3.14]);
        $this->assertEquals('PI', $result);

        // It should not be called again, testing cached
        $result = $this->taxQuoteRequest->taxQuote([3.14]);
        $this->assertEquals('PI', $result);
    }
}
