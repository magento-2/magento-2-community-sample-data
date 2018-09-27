<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Vertex;

use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Test\Unit\TestCase;

class GetTaxAreaIdTest extends TestCase
{
    public function testGetTotalTax()
    {
        $vertex = $this->getObject(ApiClient::class);

        $result = $this->invokeInaccessibleMethod($vertex, 'getTotalTax', ['TotalTax' => 5]);

        $this->assertEquals(5, $result);
    }

    public function testGetTotalTaxReturnsZeroWithoutTotalTax()
    {
        $vertex = $this->getObject(ApiClient::class);

        $result = $this->invokeInaccessibleMethod($vertex, 'getTotalTax', ['some garbage data']);

        $this->assertEquals(0, $result);
    }
}
