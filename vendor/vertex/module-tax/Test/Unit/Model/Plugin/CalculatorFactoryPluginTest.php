<?php

namespace Vertex\Tax\Test\Unit\Model\Plugin;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Data\Address;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\Tax\Model\Calculation\CalculatorFactory;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Model\Calculation\VertexCalculatorFactory;
use Vertex\Tax\Model\Calculator;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\Plugin\CalculatorFactoryPlugin;
use Vertex\Tax\Model\QuoteProviderInterface;
use Vertex\Tax\Model\TaxQuote\TaxQuoteResponse;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CalculatorFactoryPluginTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|ObjectManagerInterface */
    private $objectManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Registry */
    private $registryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|StoreManager */
    private $storeManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface */
    private $loggerInterfaceMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Calculator */
    private $calculatorMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|VertexCalculatorFactory */
    private $vertexCalculatorFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Config */
    private $configMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CalculatorFactoryPlugin */
    private $plugin;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CalculatorFactory */
    private $calculatorFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|AddressInterface */
    private $customerAddressMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|VertexCalculator */
    private $vertexCalculatorMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Store */
    private $storeMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CountryGuard */
    private $countryGuardMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Quote */
    private $quoteMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|QuoteProviderInterface */
    private $quoteProviderMock;

    protected function setUp()
    {
        parent::setUp();
        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->loggerInterfaceMock = $this->createMock(LoggerInterface::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->storeManagerMock = $this->createPartialMock(StoreManager::class, ['getStore']);
        $this->configMock = $this->createMock(Config::class);
        $this->vertexCalculatorFactoryMock = $this->createPartialMock(VertexCalculatorFactory::class, ['create']);
        $vertexCalculatorMock = $this->createMock(VertexCalculator::class);
        $this->vertexCalculatorFactoryMock->method('create')
            ->willReturn($vertexCalculatorMock);
        $this->calculatorMock = $this->createPartialMock(
            Calculator::class,
            [
                'calculateTax',
                'calculateTaxAreaIds'
            ]
        );
        $this->calculatorFactoryMock = $this->createMock(CalculatorFactory::class);
        $this->customerAddressMock = $this->createPartialMock(
            Address::class,
            [
                'getId',
                'getCity',
                'getCountryId',
                'getRegionId',
                'getRegion',
                'getPostCode'
            ]
        );
        $this->vertexCalculatorMock = $this->createMock(VertexCalculator::class);
        $this->storeMock = $this->createPartialMock(Store::class, ['getId']);
        $this->countryGuardMock = $this->createPartialMock(CountryGuard::class, ['isCountryIdServiceableByVertex']);
        $this->quoteMock = $this->createPartialMock(
            Quote::class,
            ['getShippingAddress', 'getBillingAddress', 'isVirtual']
        );
        $this->quoteMock->method('isVirtual')
            ->willReturn(false);
        $this->quoteProviderMock = $this->createPartialMock(QuoteProviderInterface::class, ['getQuote', 'setQuote']);
        $this->quoteProviderMock->method('getQuote')
            ->willReturn($this->quoteMock);
        $this->plugin = $this->getObject(CalculatorFactoryPlugin::class, [
            'logger' => $this->loggerInterfaceMock,
            'registry' => $this->registryMock,
            'storeManager' => $this->storeManagerMock,
            'config' => $this->configMock,
            'vertexCalculatorFactory' => $this->vertexCalculatorFactoryMock,
            'calculator' => $this->calculatorMock,
            'countryGuard' => $this->countryGuardMock,
            'quoteProvider' => $this->quoteProviderMock,
        ]);
    }

    public function testCalculateUnitBase()
    {
        $type = 'VERTEX_UNIT_BASE_CALCULATION';

        $proceed = function () use ($type) {
            return $type;
        };

        $quoteAddressMock = $this->createMock(Quote\Address::class);
        $quoteAddressMock->method('getCountryId')
            ->willReturn('US');
        $quoteAddressMock->method('getRegionId')
            ->willReturn(47);
        $quoteAddressMock->method('getPostcode')
            ->willReturn(44131);
        $taxQuoteResponseMock = $this->createMock(TaxQuoteResponse::class);

        $this->quoteMock->method('getShippingAddress')
            ->willReturn($quoteAddressMock);

        $this->vertexCalculatorFactoryMock->expects($this->once())
            ->method('create')
            ->with(['storeId' => 1])
            ->willReturn($this->vertexCalculatorMock);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->configMock->expects($this->once())
            ->method('isVertexActive')
            ->willReturn(true);

        $this->customerAddressMock->method('getCountryId')
            ->willReturn('US');

        $this->customerAddressMock->expects($this->exactly(3))
            ->method('getRegionId')
            ->willReturn(1);

        $this->customerAddressMock->expects($this->exactly(3))
            ->method('getPostcode')
            ->willReturn('12345');

        $this->customerAddressMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->customerAddressMock->expects($this->once())
            ->method('getCity')
            ->willReturn('New York');

        $this->calculatorMock->expects($this->once())
            ->method('calculateTax')
            ->with($quoteAddressMock)
            ->willReturn($taxQuoteResponseMock);

        $this->invokeInaccessibleMethod($this->plugin, 'isAcceptableAddress', $this->customerAddressMock);

        $this->calculatorMock->expects($this->once())
            ->method('calculateTaxAreaIds')
            ->with($this->customerAddressMock)
            ->willReturn(true);

        $this->countryGuardMock->method('isCountryIdServiceableByVertex')
            ->with('US')
            ->willReturn(true);

        $result = $this->plugin->aroundCreate(
            $this->calculatorFactoryMock,
            $proceed,
            $type,
            1,
            $this->customerAddressMock,
            $this->customerAddressMock,
            1,
            1
        );

        $this->assertEquals($this->vertexCalculatorMock, $result);
    }

    public function testCalculateWithoutUnitBase()
    {
        $type = '';

        $proceedCalled = true;

        $proceed = function () use (&$proceedCalled) {
            return $proceedCalled;
        };

        $result = $this->plugin->aroundCreate(
            $this->calculatorFactoryMock,
            $proceed,
            $type,
            1,
            $this->customerAddressMock,
            $this->customerAddressMock,
            1,
            1
        );

        $this->assertEquals($proceedCalled, $result);
    }
}
