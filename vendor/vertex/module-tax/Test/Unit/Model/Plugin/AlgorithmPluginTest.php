<?php

namespace Vertex\Tax\Test\Unit\Model\Plugin;

use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\System\Config\Source\Algorithm;
use Vertex\Tax\Model\Plugin\AlgorithmPlugin;
use Vertex\Tax\Test\Unit\TestCase;

class AlgorithmPluginTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|AlgorithmPlugin */
    private $algorithmPluginMock;

    protected function setUp()
    {
        parent::setUp();

        $this->algorithmPluginMock = $this->getObject(AlgorithmPlugin::class);

        parent::setUp();
    }

    public function testAddToOptionsArray()
    {
        $options = [
            [
                'value' => Calculation::CALC_UNIT_BASE,
                'label' => 'Unit Price'
            ],
            [
                'value' => Calculation::CALC_ROW_BASE,
                'label' => 'Row Total'
            ],
            [
                'value' => Calculation::CALC_TOTAL_BASE,
                'label' => 'Total'
            ],
        ];

        /** @var \PHPUnit_Framework_MockObject_MockObject|Algorithm $algorithmMock */
        $algorithmMock = $this->createMock(Algorithm::class);

        $result = $this->algorithmPluginMock->afterToOptionArray($algorithmMock, $options);

        $this->assertCount(4, $result);
    }
}
