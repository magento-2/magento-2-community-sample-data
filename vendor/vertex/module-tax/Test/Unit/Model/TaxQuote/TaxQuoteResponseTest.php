<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\TaxQuote;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use SebastianBergmann\Diff\Line;
use Vertex\Data\LineItem;
use Vertex\Data\Tax;
use Vertex\Services\Quote\Response;
use Vertex\Tax\Model\TaxQuote\TaxQuoteResponse;
use Vertex\Tax\Test\Unit\TestCase;

class TaxQuoteResponseTest extends TestCase
{
    /** @var Response */
    private $response;

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

        $lineItem1 = new LineItem();
        $lineItem1->setLineItemId(1);
        $lineItem1->setProductCode('PRODUCT-LINE-1');
        $lineItem1->setProductClass('Taxable Goods');
        $lineItem1->setQuantity(1);
        $lineItem1->setUnitPrice(2.50);
        $lineItem1->setTotalTax(2.50);
        $lineItem1Tax1 = new Tax();
        $lineItem1Tax1->setEffectiveRate(1.0);
        $lineItem1Tax2 = new Tax();
        $lineItem1Tax2->setEffectiveRate(2.0);
        $lineItem1->setTaxes([$lineItem1Tax1, $lineItem1Tax2]);

        $lineItem2 = new LineItem();
        $lineItem2->setTaxes([$lineItem1Tax1]);
        $lineItem2->setLineItemId(2);
        $lineItem2->setProductCode('PRODUCT-LINE-2');
        $lineItem2->setProductClass('None');
        $lineItem2->setQuantity(2);
        $lineItem2->setUnitPrice(1.50);
        $lineItem2->setTotalTax(1.50);

        $this->response = new Response();
        $this->response->setLineItems([$lineItem1, $lineItem2]);
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
        $response->prepareQuoteTaxedItems($this->response->getLineItems());

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
