<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\TaxQuote;

use Vertex\Tax\Model\TaxQuote\TaxQuoteRequest;
use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Test\Unit\TestCase;

class TaxQuoteRequestTest extends TestCase
{
    /**
     * @covers \Vertex\Tax\Model\TaxQuote\TaxQuoteRequest::__construct()
     */
    public function testConstructorThrowsNoError()
    {
        $this->getObject(TaxQuoteRequest::class);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxQuote\TaxQuoteRequest::taxQuote()
     */
    public function testTaxQuote()
    {
        $vertexMock = $this->createMock(ApiClient::class);
        $vertexMock->expects($this->once())
            ->method('sendApiRequest')
            ->willReturn('PI');

        $request = $this->getObject(TaxQuoteRequest::class, ['vertex' => $vertexMock]);

        // Vertex sendApiRequest should be called once, testing uncached
        $result = $request->taxQuote([3.14]);
        $this->assertEquals('PI', $result);

        // It should not be called again, testing cached
        $result = $request->taxQuote([3.14]);
        $this->assertEquals('PI', $result);
    }
}
