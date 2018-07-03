<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Calculation\VertexCalculator;

use Vertex\Tax\Test\Unit\TestCase;
use Vertex\Tax\Model\Calculation\VertexCalculator\GwItemKeyManager;
use Vertex\Tax\Model\TaxRegistry;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Quote\Model\Quote\Address\Item;
use Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping;

/**
 * Test gift wrapping item key management storage using the registry.
 */
class GwItemKeyManagerTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|GwItemKeyManager */
    private $gwItemKeyManagerMock;

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
        if (!class_exists(Giftwrapping::class)) {
            $this->markTestSkipped('Test only applicable to Magento 2 Commerce');

            return;
        }

        parent::setUp();

        $this->taxRegistryMock = $this->createMock(TaxRegistry::class);
        $this->quoteDetailsItemMock = $this->getMockBuilder(QuoteDetailsItemInterface::class)
            ->setMethods(
                array_merge(
                    get_class_methods(QuoteDetailsItemInterface::class),
                    ['getItemId', 'getGwId'] // These methods are only part of the extensible implementor
                )
            )
            ->getMock();
        $this->quoteItemMock = $this->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItemId'])
            ->getMock();
        $this->gwItemKeyManagerMock = $this->getObject(
            GwItemKeyManager::class,
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
        $this->quoteDetailsItemMock->method('getItemId')
            ->willReturn('1');
        $this->quoteDetailsItemMock->method('getGwId')
            ->willReturn('1');

        $this->quoteItemMock->method('getItemId')
            ->willReturn(1);

        // Verifies consistency of item ID hash generation as expected in storage
        $expected = $this->gwItemKeyManagerMock->generateGwItemMapCode($this->quoteItemMock);

        $this->taxRegistryMock->method('lookup')
            ->willReturn($expected);

        $actual = $this->gwItemKeyManagerMock->get($this->quoteDetailsItemMock);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that quote item data cannot be retrieved from storage.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager::get
     */
    public function testItemDataDoesNotExistInStorage()
    {
        $this->quoteDetailsItemMock->method('getItemId')
            ->willReturn('1');
        $this->quoteDetailsItemMock->method('getGwId')
            ->willReturn('1');

        $this->quoteItemMock->method('getItemId')
            ->willReturn(1);

        $this->assertNull($this->gwItemKeyManagerMock->get($this->quoteDetailsItemMock));
    }

    /**
     * Test that quote item ID generation is functional on an equality check.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager::generateGwItemMapCode
     */
    public function testGwItemMapCodeEquality()
    {
        $this->quoteItemMock->method('getItemId')
            ->willReturn(1);

        $expected = 'item_gw_1';
        $actual = $this->gwItemKeyManagerMock->generateGwItemMapCode($this->quoteItemMock);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that invalid quote item data fails a quote item ID equality check.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager::generateGwItemMapCode
     */
    public function testGwItemMapCodeInequality()
    {
        $this->quoteItemMock->method('getItemId')
            ->willReturn(1);

        $expected = 'item_gw_99';
        $actual = $this->gwItemKeyManagerMock->generateGwItemMapCode($this->quoteItemMock);

        $this->assertNotEquals($expected, $actual);
    }
}
