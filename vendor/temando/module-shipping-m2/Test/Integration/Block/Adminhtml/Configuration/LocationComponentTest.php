<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Block\Adminhtml\Configuration;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use Temando\Shipping\Model\LocationInterface;

/**
 * Temando Location Component Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class LocationComponentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var LocationComponent
     */
    private $block;

    /**
     * @var Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->request = $this->getMockBuilder(Request::class)
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $context = $objectManager->create(Context::class, ['request' => $this->request]);

        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);

        $this->block = $layout->createBlock(LocationComponent::class, '', ['context' => $context]);
    }

    /**
     * @test
     */
    public function getNewLocationPageUrl()
    {
        $action = 'new';
        $this->assertContains($action, $this->block->getNewLocationPageUrl());
    }

    /**
     * @test
     */
    public function getLocationIdFromRequestParams()
    {
        $locationId = '00000000-6000-0006-0000-000000000000';

        $this->request
            ->expects($this->any())
            ->method('getParam')
            ->with(LocationInterface::LOCATION_ID)
            ->willReturn($locationId);

        $this->assertEquals($locationId, $this->block->getLocationId());
    }
}
