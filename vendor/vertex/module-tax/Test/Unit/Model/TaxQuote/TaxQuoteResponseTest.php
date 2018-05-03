<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\TaxQuote;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Vertex\Tax\Model\TaxQuote\TaxQuoteResponse;
use Vertex\Tax\Test\Unit\TestCase;

class TaxQuoteResponseTest extends TestCase
{
    private $arrayResponse = [
        'LineItem' => [
            [
                'Taxes' => [
                    'EffectiveRate' => 1.0,
                    ['EffectiveRate' => 2.0]
                ],
                'lineItemId' => 1,
                'Product' => [
                    'productClass' => 'Taxable Goods',
                    '_' => 'PRODUCT-LINE-1',
                ],
                'Quantity' => ['_' => 1],
                'UnitPrice' => ['_' => 2.50],
                'TotalTax' => ['_' => 2.50],
            ],
            [
                'Taxes' => [
                    'EffectiveRate' => 1.0,
                ],
                'lineItemId' => 2,
                'Product' => [
                    'productClass' => 'None',
                    '_' => 'PRODUCT-LINE-2',
                ],
                'Quantity' => ['_' => 2],
                'UnitPrice' => ['_' => 1.50],
                'TotalTax' => 1.50,
            ]
        ]
    ];

    private $dataObjectFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->dataObjectFactory = $this->createMock(DataObjectFactory::class);
        $this->dataObjectFactory->method('create')
            ->willReturnCallback(
                function () {
                    return new DataObject();
                }
            );
    }

    /**
     * @covers \Vertex\Tax\Model\TaxQuote\TaxQuoteResponse::__construct()
     */
    public function testConstructorThrowsNoErrors()
    {
        $this->getObject(TaxQuoteResponse::class);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxQuote\TaxQuoteResponse::prepareQuoteTaxedItems()
     * @covers \Vertex\Tax\Model\TaxQuote\TaxQuoteResponse::getTaxRate()
     */
    public function testPrepareQuoteTaxedItems()
    {
        $response = $this->getObject(TaxQuoteResponse::class, ['dataObjectFactory' => $this->dataObjectFactory]);
        $response->prepareQuoteTaxedItems($this->arrayResponse['LineItem']);

        $result = $response->getQuoteTaxedItems();
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->assertTrue(isset($result[1]));
        $item = $result[1];
        $this->assertEquals('Taxable Goods', $item->getProductClass());
        $this->assertEquals('PRODUCT-LINE-1', $item->getProductSku());
        $this->assertEquals(1, $item->getProductQty());
        $this->assertEquals(2.5, $item->getUnitPrice());
        $this->assertEquals(3, $item->getTaxRate());
        $this->assertEquals(300, $item->getTaxPercent());
        $this->assertEquals(2.5, $item->getTaxAmount());

        $this->assertTrue(isset($result[2]));
        $item = $result[2];
        $this->assertEquals('None', $item->getProductClass());
        $this->assertEquals('PRODUCT-LINE-2', $item->getProductSku());
        $this->assertEquals(2, $item->getProductQty());
        $this->assertEquals(1.5, $item->getUnitPrice());
        $this->assertEquals(1, $item->getTaxRate());
        $this->assertEquals(100, $item->getTaxPercent());
        $this->assertEquals(1.5, $item->getTaxAmount());
    }
}
