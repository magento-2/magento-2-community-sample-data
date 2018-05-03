<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Vertex;

use Magento\Sales\Model\Order;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Test\Unit\TestCase;

class GetStoreIdTest extends TestCase
{
    public function testInvoiceSuppliesDataFromOrder()
    {
        $mockOrder = $this->createPartialMock(Order::class, ['getStoreId']);

        $storeId = uniqid('store-id-');

        $mockOrder->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        /** @var ApiClient $vertex */
        $vertex = $this->getObject(ApiClient::class);

        $result = $this->invokeInaccessibleMethod($vertex, 'getStoreId', $mockOrder);

        $this->assertEquals($storeId, $result);
    }

    public function testNoOrderReturnsNull()
    {
        $storeId = uniqid('store-id-');

        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')
            ->willReturn($storeId);

        $storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $storeManagerMock->method('getStore')
            ->willReturn($storeMock);

        /** @var ApiClient $vertex */
        $vertex = $this->getObject(ApiClient::class, ['storeManager' => $storeManagerMock]);

        $result = $this->invokeInaccessibleMethod($vertex, 'getStoreId', null);

        $this->assertEquals($storeId, $result);
    }
}
