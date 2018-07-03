<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Vertex\Tax\Model\Config\Source\TaxInvoice;
use Vertex\Tax\Test\Unit\TestCase;

class TaxInvoiceTest extends TestCase
{
    /**
     * @return TaxInvoice
     */
    private function createObject()
    {
        return $this->getObject(TaxInvoice::class);
    }

    public function testReturnArray()
    {
        $object = $this->createObject();
        $this->assertInternalType('array', $object->toOptionArray());
    }

    public function testImplementsOptionSourceInterface()
    {
        $object = $this->createObject();
        $this->assertInstanceOf(OptionSourceInterface::class, $object);
    }
}
