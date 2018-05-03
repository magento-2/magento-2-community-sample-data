<?php

namespace Vertex\Tax\Test\Unit\Model\Plugin;

use Magento\Framework\Registry;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Model\Sales\Quote\ItemDetails;
use Magento\Tax\Model\Sales\Total\Quote\Subtotal;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Model\Plugin\SubtotalPlugin;
use Vertex\Tax\Test\Unit\TestCase;

class SubtotalPluginTest extends TestCase
{
    /** @var SubtotalPlugin */
    private $plugin;

    /** @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface */
    private $loggerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Registry */
    private $registryMock;

    protected function setUp()
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->registryMock = $this->createMock(Registry::class);

        $this->plugin = $this->getObject(SubtotalPlugin::class, ['registry' => $this->registryMock]);
    }

    public function testAroundMapItem()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Subtotal $subtotalMock */
        $subtotalMock = $this->createMock(Subtotal::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|ItemDetails $itemDetailsMock */
        $itemDetailsMock = $this->createPartialMock(ItemDetails::class, ['setItemId']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|QuoteDetailsItemInterfaceFactory $itemInterfaceFactoryMock */
        $itemInterfaceFactoryMock = $this->createMock(QuoteDetailsItemInterfaceFactory:: class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractItem $itemMock */
        $itemMock = $this->createMock(AbstractItem::class);

        $priceIncludesTax = false;
        $useBaseCurrency = false;
        $parentCode = null;

        $proceed = function () use ($itemDetailsMock) {
            return $itemDetailsMock;
        };

        $itemMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);

        $itemDetailsMock->expects($this->once())
            ->method('setItemId')
            ->with(1)
            ->willReturn($itemDetailsMock);

        $this->registryMock->expects($this->once())
            ->method('register')
            ->with('quote_item_id_', 1, true)
            ->willReturn('void');

        $this->assertEquals(
            $itemDetailsMock,
            $this->plugin->aroundMapItem(
                $subtotalMock,
                $proceed,
                $itemInterfaceFactoryMock,
                $itemMock,
                $priceIncludesTax,
                $useBaseCurrency,
                $parentCode
            )
        );
    }
}
