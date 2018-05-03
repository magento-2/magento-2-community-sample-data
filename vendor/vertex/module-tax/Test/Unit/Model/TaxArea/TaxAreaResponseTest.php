<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\TaxArea;

use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Vertex\Tax\Model\TaxArea\TaxAreaResponse;
use Vertex\Tax\Test\Unit\TestCase;

class TaxAreaResponseTest extends TestCase
{
    private function createDataObjectFactory()
    {
        $factory = $this->createMock(DataObjectFactory::class);
        $factory->method('create')
            ->willReturnCallback(
                function () {
                    return new DataObject();
                }
            );
        return $factory;
    }

    private function createCollectionFactory()
    {
        $factory = $this->createMock(CollectionFactory::class);
        $factory->method('create')
            ->willReturnCallback(
                function () {
                    return $this->getObject(Collection::class);
                }
            );
        return $factory;
    }

    private function createTaxAreaData()
    {
        return [
            'TaxAreaResult' => [
                [
                    'taxAreaId' => 1,
                    'Jurisdiction' => [['_' => 'TAR-1'], ['_' => 'JD-1']],
                    'confidenceIndicator' => 4,
                    'PostalAddress' => 'CITY-JD1',
                ],
                [
                    'taxAreaId' => 2,
                    'Jurisdiction' => [['_' => 'TAR-1'], ['_' => 'JD-2']],
                    'confidenceIndicator' => 10,
                    'PostalAddress' => 'CITY-JD2',
                ],
                [
                    // empty tax area id for testing exclusion
                    'Jurisdiction' => [['_' => 'TAR-1'], ['_' => 'JD-0']],
                    'confidenceIndicator' => 3,
                    'PostalAddress' => 'CITY-JD0',
                ],
                [
                    'taxAreaId' => 3,
                    'Jurisdiction' => [['_' => 'TAR-1'], ['_' => 'JD-3']],
                    'confidenceIndicator' => 2,
                    'PostalAddress' => 'CITY-JD3',
                ],
                [
                    'taxAreaId' => 4,
                    'Jurisdiction' => [['_' => 'TAR-1'], ['_' => 'JD-4']],
                    'confidenceIndicator' => 3,
                    // postal address excluded to test getting it from Response object
                ],
            ],
        ];
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaResponse::__construct()
     */
    public function testConstructorThrowsNoError()
    {
        $this->getObject(TaxAreaResponse::class);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaResponse::parseResponse()
     */
    public function testParseResponse()
    {
        $taxAreaResponse = $this->getObject(TaxAreaResponse::class);

        $taxAreaResponse->parseResponse(
            [1, 2, 3],
            ['TaxAreaRequest' => ['TaxAreaLookup' => ['PostalAddress' => ['City' => 'Independence']]]]
        );

        $this->assertEquals([1, 2, 3], $taxAreaResponse->getTaxAreaResults());
        $this->assertEquals('Independence', $taxAreaResponse->getRequestCity());
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaResponse::getTaxAreaLocationsCollection()
     */
    public function testGetTaxAreaLocationsCollectionCache()
    {
        $taxAreaResponse = $this->getObject(TaxAreaResponse::class);
        $this->setInaccessibleProperty($taxAreaResponse, 'taxAreaLocations', 3.14);

        $this->assertEquals(3.14, $taxAreaResponse->getTaxAreaLocationsCollection());
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaResponse::getTaxAreaLocationsCollection()
     */
    public function testGetTaxAreaLocationsCollection()
    {
        $response = $this->createTaxAreaData();

        /** @var TaxAreaResponse $taxAreaResponse */
        $taxAreaResponse = $this->getObject(
            TaxAreaResponse::class,
            [
                'dataObjectFactory' => $this->createDataObjectFactory(),
                'dataCollectionFactory' => $this->createCollectionFactory()
            ]
        );
        $taxAreaResponse->setTaxAreaResults($response);
        $taxAreaResponse->setRequestCity('Independence');

        $collection = $taxAreaResponse->getTaxAreaLocationsCollection();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(4, $collection->getSize());

        $items = $collection->getItems();

        $item = $items[0];
        $this->assertEquals(1, $item->getTaxAreaId());
        $this->assertEquals('Jd-1, Tar-1', $item->getAreaName());
        $this->assertEquals(4, $item->getConfidenceIndicator());
        $this->assertEquals('Independence', $item->getRequestCity());
        $this->assertEquals('CITY-JD1', $item->getTaxAreaCity());

        // Tests that empty taxAreaId was skipped
        $item = $items[2];
        $this->assertEquals('CITY-JD3', $item->getTaxAreaCity());

        // Tests that with no postal address, its set to the request city
        $item = $items[3];
        $this->assertEquals('Independence', $item->getTaxAreaCity());
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaResponse::getTaxAreaLocationsCollection()
     */
    public function testNoTaxAreaResultsReturnsEmptyCollection()
    {
        $taxAreaResponse = $this->getObject(
            TaxAreaResponse::class,
            ['dataCollectionFactory' => $this->createCollectionFactory()]
        );

        $collection = $taxAreaResponse->getTaxAreaLocationsCollection();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(0, $collection->getSize());
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaResponse::getTaxAreaWithHighestConfidence()
     */
    public function testGetTaxAreaWithHighestConfidence()
    {
        $taxAreaResponse = $this->getObject(
            TaxAreaResponse::class,
            [
                'dataObjectFactory' => $this->createDataObjectFactory(),
                'dataCollectionFactory' => $this->createCollectionFactory(),
            ]
        );
        $taxAreaResponse->setTaxAreaResults($this->createTaxAreaData());
        $taxAreaResponse->setRequestCity('Independence');

        $result = $taxAreaResponse->getTaxAreaWithHighestConfidence();

        $this->assertInstanceOf(DataObject::class, $result);
        $this->assertEquals('CITY-JD2', $result->getTaxAreaCity());
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaResponse::getFirstTaxAreaInfo()
     */
    public function testGetFirstTaxAreaInfo()
    {
        $taxAreaResponse = $this->getObject(
            TaxAreaResponse::class,
            [
                'dataObjectFactory' => $this->createDataObjectFactory(),
                'dataCollectionFactory' => $this->createCollectionFactory(),
            ]
        );
        $taxAreaResponse->setTaxAreaResults($this->createTaxAreaData());
        $taxAreaResponse->setRequestCity('Independence');

        $result = $taxAreaResponse->getFirstTaxAreaInfo();

        $this->assertInstanceOf(DataObject::class, $result);
        $this->assertEquals('CITY-JD1', $result->getTaxAreaCity());
    }
}
