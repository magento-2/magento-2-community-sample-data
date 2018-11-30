<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Calculation;

use Magento\Tax\Api\Data\TaxDetailsItemInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxRateInterfaceFactory;
use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VertexCalculatorTest extends TestCase
{
    /**
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator::__construct()
     */
    public function testConstructorThrowsNoErrors()
    {
        $taxDetailsItemDataObjectFactory = $this->getMockBuilder(TaxDetailsItemInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $appliedTaxDataObjectFactory = $this->getMockBuilder(AppliedTaxInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $appliedTaxRateDataObjectFactory = $this->getMockBuilder(AppliedTaxRateInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->getObject(
            VertexCalculator::class,
            [
                'taxDetailsItemDataObjectFactory' => $taxDetailsItemDataObjectFactory,
                'appliedtaxDataObjectFactory' => $appliedTaxDataObjectFactory,
                'appliedtaxRateDataObjectFactory' => $appliedTaxRateDataObjectFactory,
            ]
        );
        $this->assertTrue(true); // no exceptions have occurred.
    }
}
