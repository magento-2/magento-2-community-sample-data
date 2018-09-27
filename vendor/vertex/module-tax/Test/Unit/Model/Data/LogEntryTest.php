<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Data;

use Vertex\Tax\Model\Data\LogEntry;
use Vertex\Tax\Test\Unit\TestCase;

class LogEntryTest extends TestCase
{
    public function testType()
    {
        $entry = $this->createEntry();
        $entry->setType('type');
        $this->assertEquals('type', $entry->getType());
        $this->assertOthersNull($entry, 'getType');
    }

    public function testCartId()
    {
        $entry = $this->createEntry();
        $entry->setCartId(2);
        $this->assertEquals(2, $entry->getCartId());
        $this->assertOthersNull($entry, 'getCartId');
    }

    public function testOrderId()
    {
        $entry = $this->createEntry();
        $entry->setOrderId(3);
        $this->assertEquals(3, $entry->getOrderId());
        $this->assertOthersNull($entry, 'getOrderId');
    }

    public function testTotalTax()
    {
        $entry = $this->createEntry();
        $entry->setTotalTax(3.14);
        $this->assertEquals(3.14, $entry->getTotalTax());
        $this->assertOthersNull($entry, 'getTotalTax');
    }

    public function testSourcePath()
    {
        $entry = $this->createEntry();
        $entry->setSourcePath('path');
        $this->assertEquals('path', $entry->getSourcePath());
        $this->assertOthersNull($entry, 'getSourcePath');
    }

    public function testTaxAreaId()
    {
        $entry = $this->createEntry();
        $entry->setTaxAreaId('id');
        $this->assertEquals('id', $entry->getTaxAreaId());
        $this->assertOthersNull($entry, 'getTaxAreaId');
    }

    public function testSubTotal()
    {
        $entry = $this->createEntry();
        $entry->setSubTotal(4);
        $this->assertEquals(4, $entry->getSubTotal());
        $this->assertOthersNull($entry, 'getSubTotal');
    }

    public function testTotal()
    {
        $entry = $this->createEntry();
        $entry->setTotal(8);
        $this->assertEquals(8, $entry->getTotal());
        $this->assertOthersNull($entry, 'getTotal');
    }

    public function testLookupResult()
    {
        $entry = $this->createEntry();
        $entry->setLookupResult('val');
        $this->assertEquals('val', $entry->getLookupResult());
        $this->assertOthersNull($entry, 'getLookupResult');
    }

    public function testDate()
    {
        $entry = $this->createEntry();
        $entry->setDate('val');
        $this->assertEquals('val', $entry->getDate());
        $this->assertOthersNull($entry, 'getDate');
    }

    public function testRequestXml()
    {
        $entry = $this->createEntry();
        $entry->setRequestXml('val');
        $this->assertEquals('val', $entry->getRequestXml());
        $this->assertOthersNull($entry, 'getRequestXml');
    }

    public function testResponseXml()
    {
        $entry = $this->createEntry();
        $entry->setResponseXml('val');
        $this->assertEquals('val', $entry->getResponseXml());
        $this->assertOthersNull($entry, 'getResponseXml');
    }

    /**
     * @return LogEntry
     */
    private function createEntry()
    {
        return $this->getObject(LogEntry::class);
    }

    /**
     * Helper method for ensuring there are no side effects on the data class
     *
     * @param LogEntry $object
     * @param string $test Method we should expect a result from
     */
    private function assertOthersNull(LogEntry $object, $test)
    {
        $methods = [
            'getType',
            'getCartId',
            'getOrderId',
            'getTotalTax',
            'getSourcePath',
            'getTaxAreaId',
            'getSubTotal',
            'getTotal',
            'getLookupResult',
            'getDate',
            'getRequestXml',
            'getResponseXml'
        ];

        foreach ($methods as $method) {
            if ($method !== $test) {
                $this->assertNull($object->{$method}());
            }
        }
    }
}
