<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Model\Dispatch;
use Temando\Shipping\Model\DocumentationInterface;

/**
 * Temando Documentation Listing Block Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DocumentationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function getEmptyDocumentationListing()
    {
        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Documentation $block */
        $block = $layout->createBlock(Documentation::class);

        $documentation = $block->getDocumentation();
        $this->assertInternalType('array', $documentation);
        $this->assertEmpty($documentation);
    }

    /**
     * @test
     */
    public function getDispatchDocumentationListing()
    {
        $this->markTestIncomplete('parent block usage and numerous constructor args to mock');

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var Dispatch $block */
        $parentBlock = $layout->createBlock(Dispatch::class);

        /** @var Documentation|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = $this->getMockBuilder(Documentation::class)
            ->setMethods(['getParentBlock'])
            ->disableOriginalConstructor()
            ->getMock();

        $block->expects($this->once())
            ->method('getParentBlock')
            ->willReturn($parentBlock);

        $block->toHtml();

        $documentation = $block->getDocumentation();
        $this->assertInternalType('array', $documentation);
        $this->assertContainsOnly(DocumentationInterface::class, $documentation);
    }

    /**
     * @test
     */
    public function getShipmentDocumentationListing()
    {
        $this->markTestIncomplete('parent block usage and numerous constructor args to mock');
    }
}
