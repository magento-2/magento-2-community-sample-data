<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Vertex\Data\LineItem;
use Vertex\Data\Tax;
use Vertex\Tax\Model\TaxQuote\TaxQuoteResponse;
use Vertex\Tax\Model\TaxRegistry;
use Vertex\Tax\Model\TaxRegistry\StorageInterface;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Test Vertex tax registry functions.
 */
class TaxRegistryTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|DataObjectFactory */
    private $dataObjectFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|StorageInterface */
    private $storageInterfaceMock;

    /** @var TaxQuoteResponse */
    private $taxDataObject;

    /** @var TaxRegistry */
    private $taxRegistry;

    /**
     * Perform test setup.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->storageInterfaceMock = $this->createMock(StorageInterface::class);

        $dataObject = $this->getObject(DataObject::class);
        $this->dataObjectFactoryMock = $this->createMock(DataObjectFactory::class);
        $this->dataObjectFactoryMock->method('create')
            ->willReturn($dataObject);

        $this->taxDataObject = $this->getObject(
            TaxQuoteResponse::class,
            ['dataObjectFactory' => $this->dataObjectFactoryMock]
        );
        $this->taxRegistry = $this->getObject(
            TaxRegistry::class,
            [
                'dataObjectFactory' => $this->dataObjectFactoryMock,
                'storage' => $this->storageInterfaceMock,
            ]
        );
    }

    /**
     * Test that tax storage can hold and return any scalar type with type integrity.
     *
     * @param array $data
     * @dataProvider provideScalarDataForRegistration
     * @covers \Vertex\Tax\Model\TaxRegistry::register()
     * @covers \Vertex\Tax\Model\TaxRegistry::lookup()
     */
    public function testScalarDataRegistration(array $data)
    {
        $expectedValues = array_values($data);

        $this->storageInterfaceMock->method('get')
            ->willReturnOnConsecutiveCalls(...$expectedValues);

        foreach ($data as $type => $expectedValue) {
            $storageKey = 'test_type_' . $type;
            $this->taxRegistry->register($storageKey, $expectedValue);

            $actualResult = $this->taxRegistry->lookup($storageKey);
            $this->assertEquals(gettype($actualResult), $type);
            $this->assertEquals($expectedValue, $actualResult);
        }
    }

    /**
     * Test that tax storage can hold and return array data with type integrity.
     *
     * @param array $data
     * @dataProvider provideArrayDataForRegistration
     * @covers \Vertex\Tax\Model\TaxRegistry::register()
     * @covers \Vertex\Tax\Model\TaxRegistry::lookup()
     */
    public function testArrayDataRegistration(array $data)
    {
        $this->storageInterfaceMock->method('get')
            ->willReturn($data);

        $storageKey = 'test_type_array';
        $this->taxRegistry->register($storageKey, $data);

        $actualResult = $this->taxRegistry->lookup($storageKey);

        $this->assertEquals(gettype($actualResult), 'array');
        $this->assertEquals(serialize($data), serialize($actualResult));
    }

    /**
     * Test that presence detection of calculated tax data in the registry responds correctly.
     *
     * @param array $data
     * @dataProvider provideTaxDataForRegistration
     * @covers \Vertex\Tax\Model\TaxRegistry::hasTaxes()
     */
    public function testTaxDataPresenceCheck(array $data)
    {
        // Calculated tax lookup should use internal object storage, not StorageInterface
        $this->storageInterfaceMock->expects($this->never())
            ->method('get');

        $this->taxDataObject->prepareQuoteTaxedItems($data);
        $this->taxRegistry->registerTaxes($this->taxDataObject);

        $this->assertTrue($this->taxRegistry->hasTaxes());
    }

    /**
     * Test that the lookupTaxes interface can hold and return calculated tax data with integrity.
     *
     * @param array $data
     * @dataProvider provideTaxDataForRegistration
     * @covers \Vertex\Tax\Model\TaxRegistry::lookupTaxes()
     */
    public function testTaxDataLookupInterface(array $data)
    {
        // Calculated tax lookup should use internal object storage, not StorageInterface
        $this->storageInterfaceMock->expects($this->never())
            ->method('get');

        $this->taxDataObject->prepareQuoteTaxedItems($data);

        $this->taxRegistry->registerTaxes($this->taxDataObject);

        $actualResult = $this->taxRegistry->lookupTaxes();

        $this->assertEquals(gettype($actualResult), 'array');

        foreach ($actualResult as $resultItem) {
            $this->assertInstanceOf(DataObject::class, $resultItem);
        }
    }

    /**
     * Test that the registerTaxes interface can hold and return calculated tax data with integrity.
     *
     * @param array $data
     * @dataProvider provideTaxDataForRegistration
     * @covers \Vertex\Tax\Model\TaxRegistry::lookupTaxes()
     */
    public function testTaxDataRegistrationInterface(array $data)
    {
        // Calculated tax lookup should use internal object storage, not StorageInterface
        $this->storageInterfaceMock->expects($this->never())
            ->method('set');

        $this->taxDataObject->prepareQuoteTaxedItems($data);
        $actualResult = $this->taxRegistry->registerTaxes($this->taxDataObject);
        $this->assertTrue($actualResult);

        $this->taxDataObject->prepareQuoteTaxedItems([]);
        $actualResult = $this->taxRegistry->registerTaxes($this->taxDataObject);
        $this->assertFalse($actualResult);
    }

    /**
     * Test that the registerError interface can hold and return data with integrity.
     *
     * @covers \Vertex\Tax\Model\TaxRegistry::registerError()
     */
    public function testErrorRegistrationInterface()
    {
        $error = 'Unable to calculate taxes. This could be caused by an invalid address provided in checkout.';

        $this->storageInterfaceMock->method('get')
            ->with(TaxRegistry::KEY_ERROR_GENERIC)
            ->willReturn($error);

        $this->taxRegistry->registerError($error, TaxRegistry::KEY_ERROR_GENERIC);

        $actualResult = $this->taxRegistry->lookupError(TaxRegistry::KEY_ERROR_GENERIC);
        $this->assertEquals($error, $actualResult);

        $this->taxRegistry->registerError($error, 'other_error_type');

        $actualResult = $this->taxRegistry->lookupError('other_error_type');
        $this->assertEquals($error, $actualResult);

        $this->taxRegistry->registerError('unexpected error message', 'another_error_type');
        $actualResult = $this->taxRegistry->lookupError('another_error_type');
        $this->assertNotNull($actualResult);
        $this->assertNotEquals($error, $actualResult);
    }

    /**
     * Test that data can be removed from registry.
     *
     * @covers \Vertex\Tax\Model\TaxRegistry::unregister()
     */
    public function testDataDeregistration()
    {
        $expectedResult = true;
        $storageKey = 'test_key';
        $testValue = 'test';

        $this->storageInterfaceMock->method('unsetData')
            ->willReturn($expectedResult);

        $this->taxRegistry->register($storageKey, $testValue);

        $actualResult = $this->taxRegistry->unregister($storageKey);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Test that the unregisterTaxes interface can remove calculated tax data from the registry.
     *
     * @param array $data
     * @dataProvider provideTaxDataForRegistration
     * @covers \Vertex\Tax\Model\TaxRegistry::unregister()
     */
    public function testTaxDataDeregistrationInterface(array $data)
    {
        $this->taxDataObject->prepareQuoteTaxedItems($data);

        $this->taxRegistry->registerTaxes($this->taxDataObject);

        $actualResult = $this->taxRegistry->unregisterTaxes();

        $this->assertTrue($actualResult);
    }

    /**
     * Data provider of scalar data for tax registry storage tests.
     *
     * @return array
     */
    public function provideScalarDataForRegistration()
    {
        return [
            'data' => [
                [
                    'boolean' => true,
                    'integer' => 99,
                    'double' => 110.50,
                    'string' => 'sample data',
                ],
            ],
        ];
    }

    /**
     * Data provider of array data for tax registry storage tests.
     *
     * @return array
     */
    public function provideArrayDataForRegistration()
    {
        return [
            'data' => [
                [
                    'id' => 1,
                    'value' => 'test',
                ],
            ],
        ];
    }

    /**
     * Data provider of mock tax response for tax registry lookup tests.
     */
    public function provideTaxDataForRegistration()
    {
        $lineItem1 = new LineItem();
        $lineItem1->setLineItemId(1);
        $lineItem1->setProductCode('PRODUCT-LINE-1');
        $lineItem1->setProductClass('Taxable Goods');
        $lineItem1->setQuantity(1);
        $lineItem1->setUnitPrice(2.50);
        $lineItem1->setTotalTax(2.50);
        $lineItem1Tax1 = new Tax();
        $lineItem1Tax1->setEffectiveRate(1.0);
        $lineItem1Tax2 = new Tax();
        $lineItem1Tax2->setEffectiveRate(2.0);
        $lineItem1->setTaxes([$lineItem1Tax1, $lineItem1Tax2]);

        $lineItem2 = new LineItem();
        $lineItem2->setTaxes([$lineItem1Tax1]);
        $lineItem2->setLineItemId(2);
        $lineItem2->setProductCode('PRODUCT-LINE-2');
        $lineItem2->setProductClass('None');
        $lineItem2->setQuantity(2);
        $lineItem2->setUnitPrice(1.50);
        $lineItem2->setTotalTax(1.50);

        return [
            [
                'data' => [
                    $lineItem1,
                    $lineItem2
                ],
            ],
        ];
    }
}
