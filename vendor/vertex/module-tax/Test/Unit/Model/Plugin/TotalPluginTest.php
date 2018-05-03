<?php

namespace Vertex\Tax\Test\Unit\Model\Plugin;

use Magento\Framework\Registry;
use Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Vertex\Tax\Model\Plugin\TotalPlugin;
use Vertex\Tax\Test\Unit\TestCase;

class TotalPluginTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|TotalPlugin */
    private $plugin;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Registry */
    private $registryMock;

    protected function setUp()
    {
        if (!class_exists(Giftwrapping::class)) {
            $this->markTestSkipped('Test only applicable to Magento 2 Commerce');
            return;
        }
        parent::setUp();
        $this->registryMock = $this->createMock(Registry::class);

        $this->plugin = $this->getObject(TotalPlugin::class, ['registry' => $this->registryMock]);
    }

    /**
     * @group commerceFeatures
     */
    public function testAroundCollect()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Giftwrapping $subject */
        $subject = $this->createMock(Giftwrapping::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Quote $quote */
        $quote = $this->createMock(Quote::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|ShippingAssignmentInterface $shippingAssignment */
        $shippingAssignment = $this->createMock(ShippingAssignmentInterface::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Quote\Address\Total $total */
        $total = $this->createMock(Quote\Address\Total::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Quote\Item $quoteItem */
        $quoteItem = $this->createMock(Quote\Item::class);
        $quoteItem->method('getItemId')
            ->willReturn(1);

        $proceed = function () {
            return [];
        };

        $shippingAssignment->expects($this->once())
            ->method('getItems')
            ->willReturn([$quoteItem]);

        $this->registryMock->expects($this->any())
            ->method('register')
            ->with(
                'quote_item_id_gw1',
                'item_gw_1',
                true
            )
            ->willReturn('void');

        $this->assertEquals([], $this->plugin->aroundCollect($subject, $proceed, $quote, $shippingAssignment, $total));
    }
}
