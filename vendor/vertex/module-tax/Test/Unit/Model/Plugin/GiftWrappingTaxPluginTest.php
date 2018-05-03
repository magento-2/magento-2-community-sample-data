<?php

namespace Vertex\Tax\Test\Unit\Plugin;

use Magento\Framework\Registry;
use Magento\GiftWrapping\Model\Total\Quote\Tax\GiftwrappingAfterTax;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Vertex\Tax\Model\Plugin\GiftWrappingTaxPlugin;
use Vertex\Tax\Test\Unit\TestCase;

class GiftWrappingTaxPluginTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|GiftWrappingTaxPlugin */
    private $plugin;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Registry */
    private $registry;

    private $subject;

    private $quote;

    private $shippingAssignment;

    private $total;

    protected function setUp()
    {
        if (!class_exists(GiftwrappingAfterTax::class)) {
            $this->markTestSkipped('Test only applicable to Magento 2 Commerce');
            return;
        }
        /** @var \PHPUnit_Framework_MockObject_MockObject|GiftwrappingAfterTax $subject */
        $this->subject = $this->createMock(GiftwrappingAfterTax::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Quote $quote */
        $this->quote = $this->createMock(Quote::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|ShippingAssignmentInterface $shippingAssignment */
        $this->shippingAssignment = $this->createMock(ShippingAssignmentInterface::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Quote\Address\Total $total */
        $this->total = $this->createPartialMock(
            Quote\Address\Total::class,
            ['getData', 'setData']
        );

        $this->registry = $this->createMock(Registry::class);

        $this->plugin = new GiftWrappingTaxPlugin(
            $this->registry
        );
    }

    /**
     * @group commerceFeatures
     */
    public function testProcessQuote()
    {
        $this->total->expects($this->exactly(3))
            ->method('getData')
            ->with('extra_taxable_details')
            ->willReturn(
                [
                    'quote_gw' => [
                        'quote' => [
                            [
                                'type' => 'quote_gw',
                                'code' => 'quote_gw',
                                'price_excl_tax' => 14.99,
                                'price_incl_tax' => 14.99,
                                'base_price_excl_tax' => 14.99,
                                'base_price_incl_tax' => 14.99,
                                'row_total_excl_tax' => 14.99,
                                'row_total_incl_tax' => 14.99,
                                'base_row_total_excl_tax' => 14.99,
                                'base_row_total_incl_tax' => 14.99,
                                'tax_percent' => 0,
                                'row_tax' => 0,
                                'base_row_tax' => 0,
                                'applied_taxes' => []
                            ]
                        ]
                    ]
                ]
            );

        $result = $this->plugin->beforeCollect($this->subject, $this->quote, $this->shippingAssignment, $this->total);

        $this->assertEquals(
            $this->total->getData('extra_taxable_details'),
            $result[2]->getData('extra_taxable_details')
        );
    }

    /**
     * @group commerceFeatures
     */
    public function testProcessItem()
    {
        $this->total->expects($this->exactly(3))
            ->method('getData')
            ->with('extra_taxable_details')
            ->willReturn(
                [
                    'item_gw' => [
                        'quote' => [
                            [
                                'type' => 'quote_gw',
                                'code' => 'quote_gw',
                                'price_excl_tax' => 14.99,
                                'price_incl_tax' => 14.99,
                                'base_price_excl_tax' => 14.99,
                                'base_price_incl_tax' => 14.99,
                                'row_total_excl_tax' => 14.99,
                                'row_total_incl_tax' => 14.99,
                                'base_row_total_excl_tax' => 14.99,
                                'base_row_total_incl_tax' => 14.99,
                                'tax_percent' => 0,
                                'row_tax' => 0,
                                'base_row_tax' => 0,
                                'applied_taxes' => []
                            ]
                        ]
                    ]
                ]
            );

        $result = $this->plugin->beforeCollect($this->subject, $this->quote, $this->shippingAssignment, $this->total);

        $this->assertEquals(
            $this->total->getData('extra_taxable_details'),
            $result[2]->getData('extra_taxable_details')
        );
    }

    /**
     * @group commerceFeatures
     */
    public function testProcessCard()
    {
        $this->total->expects($this->exactly(3))
            ->method('getData')
            ->with('extra_taxable_details')
            ->willReturn(
                [
                    'printed_card_gw' => [
                        'quote' => [
                            [
                                'type' => 'quote_gw',
                                'code' => 'quote_gw',
                                'price_excl_tax' => 14.99,
                                'price_incl_tax' => 14.99,
                                'base_price_excl_tax' => 14.99,
                                'base_price_incl_tax' => 14.99,
                                'row_total_excl_tax' => 14.99,
                                'row_total_incl_tax' => 14.99,
                                'base_row_total_excl_tax' => 14.99,
                                'base_row_total_incl_tax' => 14.99,
                                'tax_percent' => 0,
                                'row_tax' => 0,
                                'base_row_tax' => 0,
                                'applied_taxes' => []
                            ]
                        ]
                    ]
                ]
            );

        $result = $this->plugin->beforeCollect($this->subject, $this->quote, $this->shippingAssignment, $this->total);

        $this->assertEquals(
            $this->total->getData('extra_taxable_details'),
            $result[2]->getData('extra_taxable_details')
        );
    }
}
