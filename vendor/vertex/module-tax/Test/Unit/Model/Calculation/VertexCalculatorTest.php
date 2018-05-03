<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Calculation;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Registry;
use Magento\Tax\Api\Data\TaxDetailsItemInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxRateInterfaceFactory;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Sales\Total\Quote\Subtotal;
use Magento\Tax\Model\TaxDetails\ItemDetails;
use Magento\Tax\Model\Sales\Quote\ItemDetails as QuoteItemDetails;
use Magento\Tax\Model\TaxDetails\AppliedTax;
use Magento\Tax\Model\TaxDetails\AppliedTaxRate;
use Vertex\Tax\Model\ItemCode;
use Vertex\Tax\Model\ItemType;
use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VertexCalculatorTest extends TestCase
{
    /**
     * @param array $arguments
     * @return VertexCalculator
     */
    private function createCalculator($arguments = [])
    {
        $dataObjectFactory = $this->createMock(DataObjectFactory::class);
        $dataObjectFactory->method('create')
            ->willReturnCallback(
                function () {
                    return new DataObject();
                }
            );

        $detailsItemFactory = $this->createMock(TaxDetailsItemInterfaceFactory::class);
        $detailsItemFactory->method('create')
            ->willReturnCallback(
                function () {
                    return $this->getObject(ItemDetails::class);
                }
            );

        $appliedTaxDataFactory = $this->createMock(AppliedTaxInterfaceFactory::class);
        $appliedTaxDataFactory->method('create')
            ->willReturnCallback(
                function () {
                    return $this->getObject(AppliedTax::class);
                }
            );

        $appliedTaxRateDataFactory = $this->createMock(AppliedTaxRateInterfaceFactory::class);
        $appliedTaxRateDataFactory->method('create')
            ->willReturnCallback(
                function () {
                    return $this->getObject(AppliedTaxRate::class);
                }
            );

        $itemType = $this->getObject(ItemType::class);
        $itemCode = $this->getObject(ItemCode::class);

        $calculationTool = $this->createMock(Calculation::class);
        $calculationTool->method('round')
            ->willReturnCallback(
                function ($num) {
                    return round($num, 2);
                }
            );

        $calculator = $this->getObject(
            VertexCalculator::class,
            array_merge(
                [
                    'objectFactory' => $dataObjectFactory,
                    'taxDetailsItemDataObjectFactory' => $detailsItemFactory,
                    'appliedTaxDataObjectFactory' => $appliedTaxDataFactory,
                    'appliedTaxRateDataObjectFactory' => $appliedTaxRateDataFactory,
                    'itemType' => $itemType,
                    'itemCode' => $itemCode,
                    'calculationTool' => $calculationTool,
                ],
                $arguments
            )
        );

        return $calculator;
    }

    private function createTaxedItem()
    {
        $dataObject = new DataObject();

        $dataObject->setProductClass('PRODUCT_CLASS');
        $dataObject->setProductSku('SKU');
        $dataObject->setProductQty(1);
        $dataObject->setUnitPrice(3);
        $dataObject->setTaxRate(0.08);
        $dataObject->setTaxPercent(8);
        $dataObject->setBaseTaxAmount(2);
        $dataObject->setTaxAmount(2);

        return $dataObject;
    }

    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateWithTaxNotInPrice()
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateItemTax()
     */
    public function testCalculateItemTaxForProduct()
    {
        $item = $this->getObject(QuoteItemDetails::class);
        $item->setType(Subtotal::ITEM_TYPE_PRODUCT);
        $item->setCode('test');
        $item->setUnitPrice(3);

        $registry = $this->createMock(Registry::class);

        $registry->expects($this->at(1))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_QUOTE_ITEM_ID_PREFIX . 'test')
            ->willReturn(12);

        $taxedItem = $this->createTaxedItem();

        $registry->expects($this->at(0))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_LINE_ITEM_TAX_KEY)
            ->willReturn([12 => $taxedItem]);

        $calculator = $this->createCalculator(
            [
                'registry' => $registry,
            ]
        );
        $result = $this->invokeInaccessibleMethod($calculator, 'calculateWithTaxNotInPrice', $item, 2);

        $this->assertNotEquals($item, $result);
        $this->assertEquals('test', $result->getCode());
        $this->assertEquals(Subtotal::ITEM_TYPE_PRODUCT, $result->getType());
        $this->assertEquals(2, $result->getRowTax());
        $this->assertEquals(3, $result->getPrice());
        $this->assertEquals(4, $result->getPriceInclTax());
        $this->assertEquals(6, $result->getRowTotal());
        $this->assertEquals(8, $result->getRowTotalInclTax());
        $this->assertEquals(8, $result->getTaxPercent());
    }

    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateWithTaxNotInPrice()
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateItemTax()
     */
    public function testCalculateItemTaxForShipping()
    {
        $item = $this->getObject(QuoteItemDetails::class);
        $item->setType(Subtotal::ITEM_TYPE_SHIPPING);
        $item->setCode(VertexCalculator::VERTEX_SHIPPING_LINE_ITEM_ID);
        $item->setUnitPrice(3);

        $taxedItem = $this->createTaxedItem();

        $registry = $this->createMock(Registry::class);

        $registry->expects($this->at(0))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_LINE_ITEM_TAX_KEY)
            ->willReturn([VertexCalculator::VERTEX_SHIPPING_LINE_ITEM_ID => $taxedItem]);

        $registry->expects($this->at(1))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_QUOTE_ITEM_ID_PREFIX . VertexCalculator::VERTEX_SHIPPING_LINE_ITEM_ID)
            ->willReturn(VertexCalculator::VERTEX_SHIPPING_LINE_ITEM_ID);

        $calculator = $this->createCalculator(['registry' => $registry]);

        $result = $this->invokeInaccessibleMethod($calculator, 'calculateWithTaxNotInPrice', $item, 2);

        $this->assertNotEquals($item, $result);
        $this->assertEquals('shipping', $result->getCode());
        $this->assertEquals(Subtotal::ITEM_TYPE_SHIPPING, $result->getType());
        $this->assertEquals(2, $result->getRowTax());
        $this->assertEquals(3, $result->getPrice());
        $this->assertEquals(4, $result->getPriceInclTax());
        $this->assertEquals(6, $result->getRowTotal());
        $this->assertEquals(8, $result->getRowTotalInclTax());
        $this->assertEquals(8, $result->getTaxPercent());
    }

    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateWithTaxNotInPrice()
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateItemTax()
     */
    public function testCalculateItemTaxForItemLevelGiftWrap()
    {
        $item = $this->getObject(QuoteItemDetails::class);
        $item->setType('giftwrap');
        $item->setCode('gw');
        $item->setAssociatedItemCode('item');
        $item->setUnitPrice(3);

        $taxedItem = $this->createTaxedItem();

        $registry = $this->createMock(Registry::class);

        $registry->expects($this->at(0))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_LINE_ITEM_TAX_KEY)
            ->willReturn([12 => $taxedItem]);

        $registry->expects($this->at(2))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_QUOTE_ITEM_ID_PREFIX . 'item')
            ->willReturn(12);

        $registry->expects($this->at(3))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_QUOTE_ITEM_ID_PREFIX . 'gw12')
            ->willReturn(12);

        $itemType = $this->createMock(ItemType::class);
        $itemType->expects($this->once())
            ->method('giftwrap')
            ->willReturn('giftwrap');

        $calculator = $this->createCalculator(
            [
                'registry' => $registry,
                'itemType' => $itemType,
            ]
        );

        $result = $this->invokeInaccessibleMethod($calculator, 'calculateWithTaxNotInPrice', $item, 2);

        $this->assertNotEquals($item, $result);
        $this->assertEquals('gw', $result->getCode());
        $this->assertEquals('giftwrap', $result->getType());
        $this->assertEquals(2, $result->getRowTax());
        $this->assertEquals(3, $result->getPrice());
        $this->assertEquals(4, $result->getPriceInclTax());
        $this->assertEquals(6, $result->getRowTotal());
        $this->assertEquals(8, $result->getRowTotalInclTax());
        $this->assertEquals(8, $result->getTaxPercent());
    }

    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateWithTaxNotInPrice()
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateItemTax()
     */
    public function testCalculateItemTaxForOrderLevelPrintedCard()
    {
        $item = $this->getObject(QuoteItemDetails::class);
        $item->setType('type_printed_card');
        $item->setCode('lookup_card');
        $item->setUnitPrice(3);

        $taxedItem = $this->createTaxedItem();

        $registry = $this->createMock(Registry::class);

        $registry->expects($this->at(0))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_LINE_ITEM_TAX_KEY)
            ->willReturn(['code_card' => $taxedItem]);

        $itemType = $this->createMock(ItemType::class);
        $itemType->expects($this->once())
            ->method('orderPrintedCard')
            ->willReturn('type_printed_card');

        $itemCode = $this->createMock(ItemCode::class);
        $itemCode->expects($this->exactly(2))
            ->method('printedCard')
            ->willReturn('code_card');

        $calculator = $this->createCalculator(
            [
                'registry' => $registry,
                'itemType' => $itemType,
                'itemCode' => $itemCode,
            ]
        );

        $result = $this->invokeInaccessibleMethod($calculator, 'calculateWithTaxNotInPrice', $item, 1);

        $this->assertNotEquals($item, $result);
        $this->assertEquals('lookup_card', $result->getCode());
        $this->assertEquals('type_printed_card', $result->getType());
        $this->assertEquals(2, $result->getRowTax());
        $this->assertEquals(3, $result->getPrice());
        $this->assertEquals(5, $result->getPriceInclTax());
        $this->assertEquals(3, $result->getRowTotal());
        $this->assertEquals(5, $result->getRowTotalInclTax());
        $this->assertEquals(8, $result->getTaxPercent());
    }

    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateWithTaxNotInPrice()
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateItemTax()
     */
    public function testCalculateItemTaxForOrderLevelGiftWrap()
    {
        $item = $this->getObject(QuoteItemDetails::class);
        $item->setType('type_order_giftwrap');
        $item->setCode('lookup_giftwrap');
        $item->setUnitPrice(3);

        $taxedItem = $this->createTaxedItem();

        $registry = $this->createMock(Registry::class);

        $registry->expects($this->at(0))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_LINE_ITEM_TAX_KEY)
            ->willReturn(['code_giftwrap' => $taxedItem]);

        $itemType = $this->createMock(ItemType::class);
        $itemType->expects($this->once())
            ->method('orderGiftwrap')
            ->willReturn('type_order_giftwrap');

        $itemCode = $this->createMock(ItemCode::class);
        $itemCode->expects($this->exactly(2))
            ->method('giftwrap')
            ->willReturn('code_giftwrap');

        $calculator = $this->createCalculator(
            [
                'registry' => $registry,
                'itemType' => $itemType,
                'itemCode' => $itemCode,
            ]
        );

        $result = $this->invokeInaccessibleMethod($calculator, 'calculateWithTaxNotInPrice', $item, 1);

        $this->assertNotEquals($item, $result);
        $this->assertEquals('lookup_giftwrap', $result->getCode());
        $this->assertEquals('type_order_giftwrap', $result->getType());
        $this->assertEquals(2, $result->getRowTax());
        $this->assertEquals(3, $result->getPrice());
        $this->assertEquals(5, $result->getPriceInclTax());
        $this->assertEquals(3, $result->getRowTotal());
        $this->assertEquals(5, $result->getRowTotalInclTax());
        $this->assertEquals(8, $result->getTaxPercent());
    }

    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::calculateItemTax()
     */
    public function testCalculateItemTaxReturnsFactoryItemWithFakeType()
    {
        $item = $this->getObject(QuoteItemDetails::class);
        $item->setCode('test');
        $item->setType('fake');

        $registry = $this->createMock(Registry::class);
        $registry->expects($this->at(0))
            ->method('registry')
            ->with(VertexCalculator::VERTEX_QUOTE_ITEM_ID_PREFIX . 'test')
            ->willReturn(12);

        $taxedItem = $this->createTaxedItem();

        $calculator = $this->createCalculator(['registry' => $registry]);
        $result = $this->invokeInaccessibleMethod($calculator, 'calculateItemTax', $item, [12 => $taxedItem]);

        $this->assertNotEquals($taxedItem, $result);
    }

    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::__construct()
     */
    public function testConstructorThrowsNoErrors()
    {
        $this->getObject(VertexCalculator::class);
    }

    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::getVertexItemTaxes()
     */
    public function testGetVertexItemTaxes()
    {
        $registry = $this->createMock(Registry::class);
        $registry->expects($this->once())
            ->method('registry')
            ->with(VertexCalculator::VERTEX_LINE_ITEM_TAX_KEY)
            ->willReturn('test');

        $calculator = $this->createCalculator(['registry' => $registry]);

        $this->assertEquals('test', $calculator->getVertexItemTaxes());
    }

    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::getItemKey()
     */
    public function testGetItemKey()
    {
        $registry = $this->createMock(Registry::class);
        $registry->expects($this->once())
            ->method('registry')
            ->with(VertexCalculator::VERTEX_QUOTE_ITEM_ID_PREFIX . 'key')
            ->willReturn('test');

        $item = $this->getObject(QuoteItemDetails::class);
        $item->setCode('key');

        $calculator = $this->createCalculator(['registry' => $registry]);

        $this->assertEquals('test', $calculator->getItemKey($item));
    }
}
