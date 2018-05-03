<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Vertex;

use Magento\Sales\Model\Order;
use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Test\Unit\TestCase;

class GetOrderIdTest extends TestCase
{
    public function testInvoiceSuppliesDataFromOrder()
    {
        $mockOrder = $this->createPartialMock(Order::class, ['getEntityId']);

        $orderId = uniqid('order-id-');

        $mockOrder->expects($this->once())
            ->method('getEntityId')
            ->willReturn($orderId);

        /** @var ApiClient $vertex */
        $vertex = $this->getObject(ApiClient::class);

        $result = $this->invokeInaccessibleMethod($vertex, 'getOrderId', $mockOrder);

        $this->assertEquals($orderId, $result);
    }

    public function testNoOrderReturnsNull()
    {
        /** @var ApiClient $vertex */
        $vertex = $this->getObject(ApiClient::class);

        $result = $this->invokeInaccessibleMethod($vertex, 'getOrderId', null);

        $this->assertNull($result);
    }
}
