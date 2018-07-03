<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Plugin;

use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Calculator;
use Vertex\Tax\Model\TaxRegistry;
use Vertex\Tax\Model\Calculation\VertexCalculator\TaxAddressResolver;
use Vertex\Tax\Model\Plugin\QuoteTaxCollectorPlugin;
use Vertex\Tax\Test\Unit\TestCase;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Tax\Model\Sales\Total\Quote\Tax;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;

/**
 * Test quote tax collection eligibility.
 */
class QuoteTaxCollectorPluginTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|Config */
    private $configMock;

    /** @var QuoteTaxCollectorPlugin */
    private $plugin;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Quote */
    private $quoteMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ShippingAssignmentInterface */
    private $shippingAssignmentMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Calculator */
    private $taxCollectorServiceMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxAddressResolver */
    private $taxAddressResolverMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TaxRegistry */
    private $taxRegistryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Tax */
    private $taxSubjectMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Total */
    private $totalMock;

    /**
     * Perform test setup.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->taxSubjectMock = $this->createMock(Tax::class);
        $this->quoteMock = $this->createMock(Quote::class);
        $this->shippingAssignmentMock = $this->createMock(ShippingAssignmentInterface::class);
        $this->totalMock = $this->createMock(Total::class);
        $this->configMock = $this->createMock(Config::class);
        $this->taxCollectorServiceMock = $this->createMock(Calculator::class);
        $this->taxRegistryMock = $this->createMock(TaxRegistry::class);
        $this->taxAddressResolverMock = $this->createMock(TaxAddressResolver::class);
        $this->plugin = $this->getObject(
            QuoteTaxCollectorPlugin::class,
            [
                'config' => $this->configMock,
                'taxCollectorService' => $this->taxCollectorServiceMock,
                'taxAddressResolver' => $this->taxAddressResolverMock,
                'taxRegistry' => $this->taxRegistryMock,
            ]
        );
    }

    /**
     * Test whether Vertex tax collection may be used under condition of feature enablement.
     *
     * @covers \Vertex\Tax\Model\Plugin\QuoteTaxCollectorPlugin::beforeCollect()
     */
    public function testCanUseVertexCollection()
    {
        $addressMock = $this->createMock(AddressInterface::class);

        $this->configMock->method('isVertexActive')
            ->willReturn(true);

        $this->configMock->method('useVertexAlgorithm')
            ->willReturn(true);

        $this->taxAddressResolverMock->expects($this->once())
            ->method('resolve')
            ->willReturn($addressMock);

        $this->plugin->beforeCollect(
            $this->taxSubjectMock,
            $this->quoteMock,
            $this->shippingAssignmentMock,
            $this->totalMock
        );
    }

    /**
     * Test whether Vertex tax collection may not be used under condition of feature disablement.
     *
     * @covers \Vertex\Tax\Model\Plugin\QuoteTaxCollectorPlugin::beforeCollect()
     */
    public function testCannotUseVertexCollection()
    {
        $this->configMock->method('isVertexActive')
            ->willReturn(false);

        $this->configMock->method('useVertexAlgorithm')
            ->willReturn(true);

        $this->taxAddressResolverMock->expects($this->never())
            ->method('resolve');

        $this->plugin->beforeCollect(
            $this->taxSubjectMock,
            $this->quoteMock,
            $this->shippingAssignmentMock,
            $this->totalMock
        );
    }

    /**
     * Test whether Vertex tax collection may not be used under condition of invalid address data.
     *
     * @covers \Vertex\Tax\Model\Plugin\QuoteTaxCollectorPlugin::beforeCollect()
     */
    public function testCannotUseVertexCollectionByInvalidAddress()
    {
        $addressMock = $this->createMock(AddressInterface::class);

        $addressMock->method('getCountryId')
            ->willReturn('US');
        $addressMock->method('getRegionId')
            ->willReturn('51');
        $addressMock->method('getPostcode')
            ->willReturn(null);

        $this->configMock->method('isVertexActive')
            ->willReturn(true);

        $this->configMock->method('useVertexAlgorithm')
            ->willReturn(true);

        $this->taxAddressResolverMock->method('resolve')
            ->willReturn($addressMock);

        $this->taxCollectorServiceMock->expects($this->never())
            ->method('calculateTax');

        $this->plugin->beforeCollect(
            $this->taxSubjectMock,
            $this->quoteMock,
            $this->shippingAssignmentMock,
            $this->totalMock
        );
    }

    /**
     * Test whether Vertex tax collection may not be used under condition of null address.
     *
     * @covers \Vertex\Tax\Model\Plugin\QuoteTaxCollectorPlugin::beforeCollect()
     */
    public function testCannotUseVertexCollectionByNullAddress()
    {
        $this->configMock->method('isVertexActive')
            ->willReturn(true);

        $this->configMock->method('useVertexAlgorithm')
            ->willReturn(true);

        $this->taxAddressResolverMock->method('resolve')
            ->willReturn(null);

        $this->taxCollectorServiceMock->expects($this->never())
            ->method('calculateTax');

        $this->plugin->beforeCollect(
            $this->taxSubjectMock,
            $this->quoteMock,
            $this->shippingAssignmentMock,
            $this->totalMock
        );
    }
}
