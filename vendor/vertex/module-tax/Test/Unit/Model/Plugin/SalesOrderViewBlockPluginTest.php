<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Plugin;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Sales\Model\Order;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin;
use Vertex\Tax\Test\Unit\TestCase;

class SalesOrderViewBlockPluginTest extends TestCase
{
    /**
     * Ensure construction works
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::__construct()
     */
    public function testConstructionThrowsNoErrors()
    {
        $this->getObject(SalesOrderViewBlockPlugin::class);
    }

    /**
     * Ensures that the invoice button logic returns true when:
     * - Module is active AND
     * - Configuration is set to Enabled AND
     * - Order exists AND
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::shouldAddInvoiceButton()
     */
    public function testShouldAddInvoiceButtonHappyPath()
    {
        $configMock = $this->createMock(Config::class);
        $configMock->method('shouldShowManualButton')
            ->willReturn(true);
        $configMock->method('isVertexActive')
            ->willReturn(true);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getStoreId')
            ->willReturn(1);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $countryGuardMock = $this->createMock(CountryGuard::class);
        $countryGuardMock->method('isOrderServiceableByVertex')
            ->with($orderMock)
            ->willReturn(true);

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            [
                'config' => $configMock,
                'registry' => $registryMock,
                'countryGuard' => $countryGuardMock
            ]
        );
        $result = $this->invokeInaccessibleMethod($testObject, 'shouldAddInvoiceButton');

        $this->assertTrue($result);
    }

    /**
     * Ensures that the invoice button logic returns false when:
     * - Module is active AND
     * - Configuration is set to Enabled AND
     * - Order *DOES NOT EXIST*
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::shouldAddInvoiceButton()
     */
    public function testShouldAddInvoiceButtonReturnsFalseWhenNoOrder()
    {
        $configMock = $this->createMock(Config::class);
        $configMock->method('shouldShowManualButton')
            ->willReturn(true);
        $configMock->method('isVertexActive')
            ->willReturn(true);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn(null);

        $countryGuardMock = $this->createMock(CountryGuard::class);
        $countryGuardMock->method('isOrderServiceableByVertex')
            ->willReturn(true);

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            [
                'config' => $configMock,
                'registry' => $registryMock,
                'countryGuard' => $countryGuardMock
            ]
        );
        $result = $this->invokeInaccessibleMethod($testObject, 'shouldAddInvoiceButton');

        $this->assertFalse($result);
    }

    /**
     * Ensures that the invoice button logic returns false when:
     * - Module is active AND
     * - Configuration is set to *DISABLED* AND
     * - Order exists AND
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::shouldAddInvoiceButton()
     */
    public function testShouldAddInvoiceButtonReturnsFalseWhenManualInvoiceButtonIsDisabled()
    {
        $configMock = $this->createMock(Config::class);
        $configMock->method('shouldShowManualButton')
            ->willReturn(false);
        $configMock->method('isVertexActive')
            ->willReturn(true);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getStoreId')
            ->willReturn(1);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $countryGuardMock = $this->createMock(CountryGuard::class);
        $countryGuardMock->method('isOrderServiceableByVertex')
            ->with($orderMock)
            ->willReturn(true);

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            [
                'config' => $configMock,
                'registry' => $registryMock,
                'countryGuard' => $countryGuardMock
            ]
        );
        $result = $this->invokeInaccessibleMethod($testObject, 'shouldAddInvoiceButton');

        $this->assertFalse($result);
    }

    /**
     * Ensures that the invoice button logic returns false when:
     * - Module is *DISABLED* AND
     * - Configuration is set to Enabled AND
     * - Order exists AND
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::shouldAddInvoiceButton()
     */
    public function testShouldAddInvoiceButtonReturnsFalseWhenVertexIsDisabled()
    {
        $configMock = $this->createMock(Config::class);
        $configMock->method('shouldShowManualButton')
            ->willReturn(true);
        $configMock->method('isVertexActive')
            ->willReturn(false);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getStoreId')
            ->willReturn(1);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $countryGuardMock = $this->createMock(CountryGuard::class);
        $countryGuardMock->method('isOrderServiceableByVertex')
            ->with($orderMock)
            ->willReturn(true);

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            [
                'config' => $configMock,
                'registry' => $registryMock,
                'countryGuard' => $countryGuardMock
            ]
        );
        $result = $this->invokeInaccessibleMethod($testObject, 'shouldAddInvoiceButton');

        $this->assertFalse($result);
    }

    /**
     * Ensure that getOrder returns whatever is in the registry
     *
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::getOrder()
     */
    public function testGetOrderReturnsResultFromRegistry()
    {
        $rand = uniqid('r', true);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($rand);

        $testObject = $this->getObject(SalesOrderViewBlockPlugin::class, ['registry' => $registryMock]);

        $result = $this->invokeInaccessibleMethod($testObject, 'getOrder');

        $this->assertEquals($rand, $result);
    }

    /**
     * Ensure that a button is added if the block exists
     *
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::addInvoiceButton()
     */
    public function testAddInvoiceButton()
    {
        $blockMock = $this->createMock(View::class);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getEntityId')
            ->willReturn(5);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $blockMock->expects($this->once())
            ->method('addButton')
            ->with(
                'vertex_invoice',
                $this->callback(
                    function ($parameter) {
                        return is_array($parameter) && isset($parameter['label']) && isset($parameter['onclick']);
                    }
                )
            );

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            ['registry' => $registryMock]
        );
        $this->invokeInaccessibleMethod($testObject, 'addInvoiceButton', $blockMock);
    }

    /**
     * Ensure that a button is added if the block exists
     *
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::addCantInvoiceButton()
     */
    public function testAddCantInvoiceButton()
    {
        $blockMock = $this->createMock(View::class);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getEntityId')
            ->willReturn(5);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $blockMock->expects($this->once())
            ->method('addButton')
            ->with(
                'vertex_cant_invoice',
                $this->callback(
                    function ($parameter) {
                        return is_array($parameter) && isset($parameter['label']) && isset($parameter['onclick']);
                    }
                )
            );

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            ['registry' => $registryMock]
        );
        $this->invokeInaccessibleMethod($testObject, 'addCantInvoiceButton', $blockMock);
    }

    /**
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::beforeSetLayout()
     */
    public function testBeforeToHtmlHappyPath()
    {
        $blockMock = $this->createMock(View::class);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getEntityId')
            ->willReturn(5);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $blockMock->expects($this->once())
            ->method('addButton')
            ->with(
                'vertex_invoice',
                $this->callback(
                    function ($parameter) {
                        return is_array($parameter) && isset($parameter['label']) && isset($parameter['onclick']);
                    }
                )
            );

        $configMock = $this->createMock(Config::class);
        $configMock->method('shouldShowManualButton')
            ->willReturn(true);
        $configMock->method('isVertexActive')
            ->willReturn(true);

        $countryGuardMock = $this->createMock(CountryGuard::class);
        $countryGuardMock->method('isOrderServiceableByVertex')
            ->with($orderMock)
            ->willReturn(true);

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            [
                'config' => $configMock,
                'countryGuard' => $countryGuardMock,
                'registry' => $registryMock,
            ]
        );
        $testObject->beforeSetLayout($blockMock, null);
    }

    /**
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::beforeSetLayout()
     */
    public function testBeforeSetLayoutRestrictivePath()
    {
        $blockMock = $this->createMock(View::class);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getEntityId')
            ->willReturn(5);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $blockMock->expects($this->once())
            ->method('addButton')
            ->with(
                'vertex_cant_invoice',
                $this->callback(
                    function ($parameter) {
                        return is_array($parameter) && isset($parameter['label']) && isset($parameter['onclick']);
                    }
                )
            );

        $configMock = $this->createMock(Config::class);
        $configMock->method('shouldShowManualButton')
            ->willReturn(true);
        $configMock->method('isVertexActive')
            ->willReturn(true);

        $countryGuardMock = $this->createMock(CountryGuard::class);
        $countryGuardMock->method('isOrderServiceableByVertex')
            ->with($orderMock)
            ->willReturn(false);

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            [
                'config' => $configMock,
                'countryGuard' => $countryGuardMock,
                'registry' => $registryMock,
            ]
        );
        $testObject->beforeSetLayout($blockMock, null);
    }

    /**
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::getInvoiceUrl()
     */
    public function testGetInvoiceUrl()
    {
        $orderId = mt_rand();

        $urlBuilderMock = $this->createMock(UrlInterface::class);
        $urlBuilderMock->expects($this->atLeastOnce())
            ->method('getUrl')
            ->with(
                $this->callback(
                    function ($parameter) {
                        return is_string($parameter);
                    }
                ),
                $this->callback(
                    function ($parameter) use ($orderId) {
                        return $parameter['order_id'] === $orderId;
                    }
                )
            );

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getEntityId')
            ->willReturn($orderId);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            [
                'urlBuilder' => $urlBuilderMock,
                'registry' => $registryMock
            ]
        );
        $this->invokeInaccessibleMethod($testObject, 'getInvoiceUrl');
    }

    /**
     * Ensures that canInvoice returns true when isOrderServiceableByVertex does
     *
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::canInvoice()
     */
    public function testCanInvoice()
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getEntityId')
            ->willReturn(5);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $countryGuardMock = $this->createMock(CountryGuard::class);
        $countryGuardMock->method('isOrderServiceableByVertex')
            ->willReturn(true);

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            ['countryGuard' => $countryGuardMock, 'registry' => $registryMock]
        );
        $result = $this->invokeInaccessibleMethod($testObject, 'canInvoice');

        $this->assertTrue($result);
    }

    /**
     * Ensures that canInvoice returns false when isOrderServiceableByVertex does
     *
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Model\Plugin\SalesOrderViewBlockPlugin::canInvoice()
     */
    public function testCantInvoice()
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getEntityId')
            ->willReturn(5);

        $registryMock = $this->createMock(Registry::class);
        $registryMock->method('registry')
            ->with('sales_order')
            ->willReturn($orderMock);

        $countryGuardMock = $this->createMock(CountryGuard::class);
        $countryGuardMock->method('isOrderServiceableByVertex')
            ->willReturn(false);

        $testObject = $this->getObject(
            SalesOrderViewBlockPlugin::class,
            ['countryGuard' => $countryGuardMock, 'registry' => $registryMock]
        );
        $result = $this->invokeInaccessibleMethod($testObject, 'canInvoice');

        $this->assertFalse($result);
    }
}
