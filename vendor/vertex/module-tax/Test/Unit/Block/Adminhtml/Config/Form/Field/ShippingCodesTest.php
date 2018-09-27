<?php

namespace Vertex\Tax\Test\Unit\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Escaper;
use Magento\OfflineShipping\Model\Carrier\Flatrate;
use Magento\OfflineShipping\Model\Carrier\Tablerate;
use Magento\Shipping\Model\Config;
use Magento\Usps\Model\Carrier;
use Vertex\Tax\Block\Adminhtml\Config\Form\Field\ShippingCodes;
use Vertex\Tax\Test\Unit\TestCase;

class ShippingCodesTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|ShippingCodes */
    private $blockMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Context */
    private $contextMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Config */
    private $configMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractElement */
    private $abstractElementMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Tablerate */
    private $tableRateMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Flatrate */
    private $flatRateMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Carrier */
    private $uspsMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Escaper */
    private $escaperMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ScopeConfigInterface */
    private $scopeConfigInterfaceMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|RequestInterface */
    private $requestMock;

    protected function setUp()
    {
        parent::setUp();
        $this->contextMock = $this->createPartialMock(Context::class, ['getScopeConfig', 'getEscaper', 'getRequest']);
        $this->configMock = $this->createPartialMock(Config::class, ['getActiveCarriers']);
        $this->abstractElementMock = $this->createMock(AbstractElement::class);
        $this->tableRateMock = $this->createPartialMock(Tablerate::class, ['getAllowedMethods']);
        $this->flatRateMock = $this->createPartialMock(Flatrate::class, ['getAllowedMethods']);
        $this->scopeConfigInterfaceMock = $this->createMock(ScopeConfigInterface::class);
        $this->uspsMock = $this->createMock(Carrier::class);
        $this->escaperMock = $this->createMock(Escaper::class);
        $this->requestMock = $this->createPartialMock(Http::class, ['getParam']);
        $this->escaperMock->method('escapeHtml')
            ->willReturnCallback(
                function ($html) {
                    return $html;
                }
            );
        $this->contextMock->method('getEscaper')
            ->willReturn($this->escaperMock);
        $this->contextMock->method('getScopeConfig')
            ->willReturn($this->scopeConfigInterfaceMock);
        $this->contextMock->method('getRequest')
            ->willReturn($this->requestMock);

        $this->blockMock = $this->getObject(
            ShippingCodes::class,
            [
                'context' => $this->contextMock,
                'shippingConfig' => $this->configMock,
            ]
        );

        $this->requestMock->method('getParam')
            ->willReturn(null);
    }

    public function testGetElementHtml()
    {
        $expected = '<table cellspacing="0" class="data-grid"><thead><tr><th class="data-grid-th">Shipping Method</th>'
            . '<th class="data-grid-th">Product Code</th></tr></thead><tbody><tr>'
            . '<th class="data-grid-th"   colspan="2">usps</th></tr><tr class="" >'
            . '<td class="label"  style="padding:1rem;" >Priority Mail: </td>'
            . '<td class="value" style="padding:1rem;" > usps_usps_1</td></tr>'
            . '<tr><td class="label"  style="padding:1rem;">Priority Mail: </td>'
            . '<td class="value" style="padding:1rem;" > usps_usps_1</td></tr></tbody></table>';

        $this->configMock->expects($this->once())
            ->method('getActiveCarriers')
            ->willReturn(
                [
                    'flatrate' => $this->flatRateMock,
                    'tablerate' => $this->tableRateMock,
                    'usps' => $this->uspsMock
                ]
            );

        $this->uspsMock->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn(
                [
                    'usps_1' => 'Priority Mail',
                ]
            );

        $result = $this->blockMock->getElementHtml();

        $this->assertEquals($expected, $result);
    }

    public function testRender()
    {
        $expected = '<tr id="row_"><td><table cellspacing="0" class="data-grid">'
            . '<thead><tr><th class="data-grid-th">Shipping Method</th><th class="data-grid-th">Product Code</th></tr>'
            . '</thead><tbody></tbody></table></td></tr>';

        $this->configMock->expects($this->once())
            ->method('getActiveCarriers')
            ->willReturn(
                [
                    'flatrate' => $this->flatRateMock,
                    'tablerate' => $this->tableRateMock
                ]
            );

        $result = $this->blockMock->render($this->abstractElementMock);

        $this->assertEquals($expected, $result);
    }
}
