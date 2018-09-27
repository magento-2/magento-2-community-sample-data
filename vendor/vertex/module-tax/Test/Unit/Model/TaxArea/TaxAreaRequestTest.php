<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\TaxArea;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Vertex\Tax\Model\TaxArea\TaxAreaRequest;
use Vertex\Tax\Model\TaxArea\TaxAreaResponse;
use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Model\TaxArea\TaxAreaResponseFactory;
use Vertex\Tax\Test\Unit\TestCase;

class TaxAreaRequestTest extends TestCase
{
    private function createStoreManager()
    {
        $store = $this->createMock(Store::class);
        $store->method('getId')
            ->willReturn(0);

        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')
            ->willReturn($store);

        return $storeManager;
    }

    private function getAddress()
    {
        return [
            'StreetAddress1' => '1500 Cherry St',
            'StreetAddress2' => 'Suite M',
            'Country' => 'USA',
            'City' => 'Louisville',
            'MainDivision' => 'CO',
            'PostalCode' => '80027',
        ];
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaRequest::__construct()
     */
    public function testConstructorThrowsNoError()
    {
        $this->getObject(TaxAreaRequest::class);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaRequest::taxAreaLookup()
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaRequest::getFormattedRequest()
     */
    public function testFreshTaxAreaLookup()
    {
        $address = $this->getAddress();

        $vertexMock = $this->createMock(ApiClient::class);
        $vertexMock->expects($this->once())
            ->method('performRequest')
            ->with(
                $this->callback(
                    function ($requestData) use ($address) {
                        return $requestData['TaxAreaRequest']['TaxAreaLookup']['PostalAddress'] == $address;
                    }
                ),
                'tax_area_lookup'
            )
            ->willReturn(['test']);

        $taxAreaResponse = $this->createMock(TaxAreaResponse::class);
        $taxAreaResponse->expects($this->once())
            ->method('parseResponse');

        $taxAreaResponseFactory = $this->createMock(TaxAreaResponseFactory::class);
        $taxAreaResponseFactory->method('create')
            ->willReturn($taxAreaResponse);

        /** @var TaxAreaRequest $taxAreaRequest */
        $taxAreaRequest = $this->getObject(
            TaxAreaRequest::class,
            [
                'storeManager' => $this->createStoreManager(),
                'vertex' => $vertexMock,
                'responseFactory' => $taxAreaResponseFactory,
            ]
        );
        $response = $taxAreaRequest->taxAreaLookup($address);

        $this->assertEquals($response, $taxAreaResponse);
    }

    /**
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaRequest::taxAreaLookup()
     * @covers \Vertex\Tax\Model\TaxArea\TaxAreaRequest::getRequestCacheKey()
     */
    public function testCachedTaxAreaLookup()
    {
        $taxAreaRequest = $this->getObject(
            TaxAreaRequest::class,
            [
                'storeManager' => $this->createStoreManager(),
            ]
        );

        $cacheKey = $this->invokeInaccessibleMethod($taxAreaRequest, 'getRequestCacheKey', $this->getAddress());
        $this->setInaccessibleProperty($taxAreaRequest, 'requestCache', [$cacheKey => '2.18']);

        $response = $taxAreaRequest->taxAreaLookup($this->getAddress());

        $this->assertEquals(2.18, $response);
    }
}
