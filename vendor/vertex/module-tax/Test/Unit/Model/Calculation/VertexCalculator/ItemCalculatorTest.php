<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Calculation\VertexCalculator;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Tax\Model\Sales\Quote\ItemDetails;
use Vertex\Tax\Model\Calculation\VertexCalculator\ItemCalculator;
use Vertex\Tax\Test\Unit\TestCase;

/**
 *
 */
class ItemCalculatorTest extends TestCase
{
    private function getCalculator()
    {
        $dataObjectFactory = $this->getMockBuilder(DataObjectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockCurrency = $this->getMockBuilder(PriceCurrencyInterface::class)
            ->setMethods(['round'])
            ->getMockForAbstractClass();

        $mockCurrency->method('round')
            ->willReturnCallback(
                function ($num) {
                    return round($num, 2);
                }
            );

        return $this->getObject(
            ItemCalculator::class,
            ['objectFactory' => $dataObjectFactory, 'currency' => $mockCurrency]
        );
    }

    /**
     * Test data retrieval and storage from itemTaxes
     *
     * This tests that the itemTax object successfully gets data from the itemTaxes and stores it on the itemTax object
     */
    public function testUpdateItemTaxFromKey()
    {
        $testData = uniqid('test');
        $testKey = uniqid('key');
        $testDataKey = uniqid('datakey');
        $keyData = new DataObject([$testDataKey => $testData]);
        $itemTax = new DataObject();
        $itemTaxes = [$testKey => $keyData];

        $calculator = $this->getCalculator();
        $this->invokeInaccessibleMethod(
            $calculator,
            'updateItemTaxFromKey',
            $itemTax,
            $itemTaxes,
            $testKey
        );

        $this->assertEquals($testData, $itemTax->getData($testDataKey));
    }

    /**
     * Test that we re-calculate the tax if the unit price does not match what we expect
     *
     * @dataProvider provideDataForNonBaseCurrencyTest
     *
     * @param array $itemTax
     * @param array $item
     * @param int $expectedTaxAmount
     */
    public function testConvertNonBaseCurrency($itemTax, $item, $expectedTaxAmount)
    {
        $itemTax = new DataObject($itemTax);
        $itemDetails = $this->getObject(ItemDetails::class);
        $itemDetails->setUnitPrice($item['unit_price']);
        $itemDetails->setQuantity($item['quantity']);

        $calculator = $this->getCalculator();
        $this->invokeInaccessibleMethod(
            $calculator,
            'convertNonBaseCurrency',
            $itemTax,
            $itemDetails
        );

        $this->assertEquals($expectedTaxAmount, $itemTax->getTaxAmount());
    }

    /**
     * Provide data for {@see testConvertNonBaseCurrency}
     *
     * @return array
     */
    public function provideDataForNonBaseCurrencyTest()
    {
        return [
            [
                'itemTax' => ['unit_price' => 5, 'tax_rate' => 0.5, 'tax_amount' => 1],
                'item' => ['unit_price' => 10, 'quantity' => 1],
                'expectedTaxAmount' => 5,
            ],
            [
                'itemTax' => ['unit_price' => 5, 'tax_rate' => 0.5, 'tax_amount' => 1],
                'item' => ['unit_price' => 10, 'quantity' => 2],
                'expectedTaxAmount' => 10,
            ],
        ];
    }
}
