<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Calculation\VertexCalculator;

use Vertex\Tax\Test\Unit\TestCase;
use Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager;
use Vertex\Tax\Model\TaxRegistry;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Quote\Model\Quote\Address\Item;

/**
 * Test item key management storage using the registry.
 */
class ItemKeyManagerTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|ItemKeyManager */
    private $itemKeyManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|QuoteDetailsItemInterface */
    private $quoteDetailsItemMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Item */
    private $quoteItemMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxRegistry */
    private $taxRegistryMock;

    /**
     * Setup test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->taxRegistryMock = $this->createMock(TaxRegistry::class);
        $this->quoteDetailsItemMock = $this->createMock(QuoteDetailsItemInterface::class);
        $this->quoteItemMock = $this->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getQuoteId', 'getProductId', 'getQty'])
            ->getMock();
        $this->itemKeyManagerMock = $this->getObject(
            ItemKeyManager::class,
            ['taxRegistry' => $this->taxRegistryMock]
        );
    }

    /**
     * Test that quote item data can be retrieved from storage.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager::get
     */
    public function testItemDataExistsInStorage()
    {
        $this->quoteDetailsItemMock->method('getCode')
            ->willReturn('sequence-1');
        $this->quoteDetailsItemMock->method('getParentCode')
            ->willReturn(null);
        $this->quoteDetailsItemMock->method('getType')
            ->willReturn('product');

        $this->quoteItemMock->method('getId')
            ->willReturn(null);
        $this->quoteItemMock->method('getQuoteId')
            ->willReturn(2);
        $this->quoteItemMock->method('getProductId')
            ->willReturn(3);
        $this->quoteItemMock->method('getQty')
            ->willReturn(1);

        // Verifies consistency of item ID hash generation as expected in storage
        $expected = $this->itemKeyManagerMock->createQuoteItemHash($this->quoteItemMock);

        $this->taxRegistryMock->method('lookup')
            ->willReturn($expected);

        $actual = $this->itemKeyManagerMock->get($this->quoteDetailsItemMock);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that quote item data cannot be retrieved from storage.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager::get
     */
    public function testItemDataDoesNotExistInStorage()
    {
        $this->quoteDetailsItemMock->method('getCode')
            ->willReturn('sequence-1');
        $this->quoteDetailsItemMock->method('getParentCode')
            ->willReturn(null);
        $this->quoteDetailsItemMock->method('getType')
            ->willReturn('product');

        $this->quoteItemMock->method('getId')
            ->willReturnOnConsecutiveCalls(null);
        $this->quoteItemMock->method('getQuoteId')
            ->willReturn(2);
        $this->quoteItemMock->method('getProductId')
            ->willReturn(3);
        $this->quoteItemMock->method('getQty')
            ->willReturn(1);

        $this->assertNull($this->itemKeyManagerMock->get($this->quoteDetailsItemMock));
    }

    /**
     * Test that quote item ID generation is functional on an equality check.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager::createQuoteItemHash
     */
    public function testQuoteItemHashEquality()
    {
        $this->quoteItemMock->method('getId')
            ->willReturnOnConsecutiveCalls(99);
        $this->quoteItemMock->method('getQuoteId')
            ->willReturn(25);
        $this->quoteItemMock->method('getProductId')
            ->willReturn(14);
        $this->quoteItemMock->method('getQty')
            ->willReturn(2);

        $expected = 'a1df3845798665ae6dfd1e315e93e7a4d182641d';
        $actual = $this->itemKeyManagerMock->createQuoteItemHash($this->quoteItemMock);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that invalid quote item data fails a quote item ID equality check.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager::createQuoteItemHash
     */
    public function testQuoteItemHashInequality()
    {
        $this->quoteItemMock->method('getId')
            ->willReturnOnConsecutiveCalls(null);
        $this->quoteItemMock->method('getQuoteId')
            ->willReturn(8);
        $this->quoteItemMock->method('getProductId')
            ->willReturn(2);
        $this->quoteItemMock->method('getQty')
            ->willReturn(10);

        $expected = 'eadc1dd8fc279583d5552700ae5d248e3fa123bd';
        $actual = $this->itemKeyManagerMock->createQuoteItemHash($this->quoteItemMock);

        $this->assertNotEquals($expected, $actual);
    }
}
