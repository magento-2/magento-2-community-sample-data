<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Plugin;

use Magento\Tax\Model\Calculation\CalculatorFactory;
use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Model\Calculation\VertexCalculatorFactory;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\TaxRegistry;
use Vertex\Tax\Model\Plugin\CalculatorFactoryPlugin;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Test calculator factory generation.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CalculatorFactoryPluginTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxRegistry */
    private $taxRegistryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|VertexCalculatorFactory */
    private $vertexCalculatorFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Config */
    private $configMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CalculatorFactoryPlugin */
    private $plugin;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CalculatorFactory */
    private $calculatorFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|VertexCalculator */
    private $vertexCalculatorMock;

    /**
     * Perform test setup.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->taxRegistryMock = $this->createMock(TaxRegistry::class);
        $this->configMock = $this->createMock(Config::class);
        $this->vertexCalculatorFactoryMock = $this->createPartialMock(VertexCalculatorFactory::class, ['create']);
        $this->calculatorFactoryMock = $this->createMock(CalculatorFactory::class);
        $this->vertexCalculatorMock = $this->createMock(VertexCalculator::class);
        $this->plugin = $this->getObject(CalculatorFactoryPlugin::class, [
            'config' => $this->configMock,
            'taxRegistry' => $this->taxRegistryMock,
            'vertexCalculatorFactory' => $this->vertexCalculatorFactoryMock,
        ]);
    }

    /**
     * Test Vertex calculator generation under condition of tax data being present in the registry.
     *
     * @covers \Vertex\Tax\Model\Plugin\CalculatorFactoryPlugin::aroundCreate()
     */
    public function testCanUseVertexCalculator()
    {
        $this->taxRegistryMock->expects($this->once())
            ->method('hasTaxes')
            ->willReturn(true);

        $this->vertexCalculatorFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->vertexCalculatorMock);

        $proceed = function () {
            return true;
        };

        $this->plugin->aroundCreate($this->calculatorFactoryMock, $proceed, null, 1);
    }

    /**
     * Test stock calculator generation under condition of tax data being absent from the registry.
     *
     * @covers \Vertex\Tax\Model\Plugin\CalculatorFactoryPlugin::aroundCreate()
     */
    public function testCannotUseVertexCalculator()
    {
        $this->taxRegistryMock->expects($this->once())
            ->method('hasTaxes')
            ->willReturn(false);

        $this->vertexCalculatorFactoryMock->expects($this->never())
            ->method('create')
            ->willReturn($this->vertexCalculatorMock);

        $proceed = function () {
            return true;
        };

        $this->plugin->aroundCreate($this->calculatorFactoryMock, $proceed, null, 1);
    }
}
