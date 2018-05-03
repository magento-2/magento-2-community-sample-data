<?php

namespace Vertex\Tax\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\CredentialChecker;
use Vertex\Tax\Test\Unit\TestCase;

class VertexStatusTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|Context */
    private $contextMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractElement */
    private $abstractElementMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Config */
    private $configMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|VertexStatus */
    private $block;

    /** @var \PHPUnit_Framework_MockObject_MockObject|VertexStatus */
    private $blockMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Http */
    private $httpMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CredentialChecker */
    private $credentialCheckerMock;

    protected function setUp()
    {
        parent::setUp();

        $this->contextMock = $this->createPartialMock(Context::class, ['getRequest']);
        $this->configMock = $this->createMock(Config::class);
        $this->abstractElementMock = $this->createMock(AbstractElement::class);
        $this->httpMock = $this->createPartialMock(Http::class, ['getParam']);
        $this->blockMock = $this->createPartialMock(VertexStatus::class, ['getRequest']);
        $this->credentialCheckerMock = $this->createPartialMock(CredentialChecker::class, ['validate']);

        $this->contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->httpMock);

        $this->block = $this->getObject(
            VertexStatus::class,
            [
                'context' => $this->contextMock,
                'config' => $this->configMock,
                'credentialChecker' => $this->credentialCheckerMock,
            ]
        );
    }

    public function testStatusDisabled()
    {
        $expected = '<span class="grid-severity-critical"><span>Disabled</span></span>';
        $actual = $this->invokeInaccessibleMethod($this->block, '_getElementHtml', $this->abstractElementMock);

        $this->assertEquals($expected, $actual);
    }

    public function testStatusNotValid()
    {
        $expected = '<span class="grid-severity-minor"><span>Invalid</span></span>';
        $this->configMock->expects($this->once())
            ->method('isVertexActive')
            ->willReturn(true);

        $credentialResult = $this->getObject(CredentialChecker\Result::class)
            ->setValid(false)
            ->setMessage('Invalid')
            ->setArguments([]);

        $this->credentialCheckerMock->method('validate')
            ->willReturn($credentialResult);

        $actual = $this->invokeInaccessibleMethod($this->block, '_getElementHtml', $this->abstractElementMock);

        $this->assertEquals($expected, $actual);
    }

    public function testStatusIsValid()
    {
        $expected = '<span class="grid-severity-notice"><span>Valid</span></span>';
        $this->configMock->expects($this->once())
            ->method('isVertexActive')
            ->willReturn(true);

        $credentialResult = $this->getObject(CredentialChecker\Result::class)
            ->setValid(true);

        $this->credentialCheckerMock->method('validate')
            ->willReturn($credentialResult);

        $actual = $this->invokeInaccessibleMethod($this->block, '_getElementHtml', $this->abstractElementMock);
        $this->assertEquals($expected, $actual);
    }
}
