<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Plugin;

use Magento\Framework\Registry;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Model\Sales\Quote\ItemDetails;
use Magento\Tax\Model\Sales\Total\Quote\Subtotal;
use Vertex\Tax\Model\Plugin\SubtotalPlugin;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Test subtotal item mapping plugin.
 */
class SubtotalPluginTest extends TestCase
{
    /** @var SubtotalPlugin */
    private $plugin;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Registry */
    private $registryMock;

    /**
     * Setup test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->registryMock = $this->createMock(Registry::class);
        $this->plugin = $this->getObject(SubtotalPlugin::class, ['registry' => $this->registryMock]);
    }

    /**
     * Test plugin injection point for errors.
     */
    public function testAroundMapItem()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Subtotal $subtotalMock */
        $subtotalMock = $this->createMock(Subtotal::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|ItemDetails $itemDetailsMock */
        $itemDetailsMock = $this->createMock(ItemDetails::class);

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
