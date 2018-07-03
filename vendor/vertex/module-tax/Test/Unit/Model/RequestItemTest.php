<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model;

use Vertex\Tax\Model\ZipCodeFixer;
use Vertex\Tax\Model\RequestItem;
use Vertex\Tax\Test\Unit\TestCase;

class RequestItemTest extends TestCase
{
    /**
     * @covers \Vertex\Tax\Model\RequestItem::__construct()
     */
    public function testConstructThrowsNoErrors()
    {
        $this->getObject(RequestItem::class);
    }

    /**
     * @covers \Vertex\Tax\Model\RequestItem::exportAsArray()
     */
    public function testExport()
    {
        $item = $this->getObject(RequestItem::class);
        $item->setRequestType('request_type');
        $item->setTrustedId('trusted_id');
        $item->setDocumentDate('2017-01-01');
        $item->setPostingDate('2017-01-02');
        $item->setTransactionType('transaction_type');
        $item->setDocumentNumber('document_number');
        $item->setOrderItems([]);

        $result = $item->exportAsArray();
        $this->assertEquals(
            [
                'Login' => ['TrustedId' => 'trusted_id'],
                'request_type' => [
                    'documentDate' => '2017-01-01',
                    'postingDate' => '2017-01-02',
                    'transactionType' => 'transaction_type',
                    'documentNumber' => 'document_number',
                    'LineItem' => [],
                ]
            ],
            $result
        );
    }

    /**
     * @covers \Vertex\Tax\Model\RequestItem::getFormattedItems()
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testAddItems()
    {
        $testItems = [
            'k1' => [
                'product_class' => 'CLASS-1',
                'product_code' => 'CODE-17890123456789012345678901234567890m',
                'price' => 5,
                'qty' => 1,
                'extended_price' => 5,
            ],
            'k2' => [
                'product_class' => 'CLASS-2',
                'product_code' => 'CODE-27890123456789012345678901234567890m',
                'price' => 4,
                'qty' => 2,
                'extended_price' => 8,
            ],
        ];

        $zipCodeFixer = $this->getObject(ZipCodeFixer::class);

        /** @var RequestItem $requestItem */
        $requestItem = $this->getObject(
            RequestItem::class,
            ['zipCodeFixer' => $zipCodeFixer]
        );
        $requestItem->setLocationCode('location_code');
        $requestItem->setCompanyId('company_id');
        $requestItem->setCompanyStreet1('Strt 1');
        $requestItem->setCompanyStreet2('Strt 2');
        $requestItem->setCompanyCity('City');
        $requestItem->setCompanyState('ST');
        $requestItem->setCompanyPostcode('12345');
        $requestItem->setCompanyCountry('USA');
        $requestItem->setCustomerClass('cust_class');
        $requestItem->setCustomerCode('cust_code');
        $requestItem->setCustomerStreet1('cu_st_1');
        $requestItem->setCustomerStreet2('cu_st_2');
        $requestItem->setCustomerCity('cu_city');
        $requestItem->setCustomerRegion('cu_regn');
        $requestItem->setCustomerPostcode('54321');
        $requestItem->setCustomerCountry('usa');

        $queryItems = $requestItem->getFormattedItems($testItems);

        $this->assertInternalType('array', $queryItems);
        $this->assertCount(2, $queryItems);
        $this->assertEquals(
            [
                'lineItemNumber' => 1,
                'lineItemId' => 'k1',
                'locationCode' => 'location_code',
                'Seller' => [
                    'Company' => 'company_id',
                    'PhysicalOrigin' => [
                        'StreetAddress1' => 'Strt 1',
                        'StreetAddress2' => 'Strt 2',
                        'City' => 'City',
                        'Country' => 'USA',
                        'MainDivision' => 'ST',
                        'PostalCode' => '12345',
                    ],
                ],
                'Customer' => [
                    'CustomerCode' => [
                        'classCode' => 'cust_class',
                        '_' => 'cust_code',
                    ],
                    'Destination' => [
                        'StreetAddress1' => 'cu_st_1',
                        'StreetAddress2' => 'cu_st_2',
                        'City' => 'cu_city',
                        'MainDivision' => 'cu_regn',
                        'PostalCode' => '54321',
                        'Country' => 'usa',
                    ],
                ],
                'Product' => [
                    'productClass' => 'CLASS-1',
                    '_' => 'CODE-17890123456789012345678901234567890',
                ],
                'UnitPrice' => 5,
                'Quantity' => 1,
                'ExtendedPrice' => 5,
            ],
            $queryItems[0]
        );
        $this->assertEquals(
            [
                'lineItemNumber' => 2,
                'lineItemId' => 'k2',
                'locationCode' => 'location_code',
                'Seller' => [
                    'Company' => 'company_id',
                    'PhysicalOrigin' => [
                        'StreetAddress1' => 'Strt 1',
                        'StreetAddress2' => 'Strt 2',
                        'City' => 'City',
                        'Country' => 'USA',
                        'MainDivision' => 'ST',
                        'PostalCode' => 12345,
                    ],
                ],
                'Customer' => [
                    'CustomerCode' => [
                        'classCode' => 'cust_class',
                        '_' => 'cust_code',
                    ],
                    'Destination' => [
                        'StreetAddress1' => 'cu_st_1',
                        'StreetAddress2' => 'cu_st_2',
                        'City' => 'cu_city',
                        'MainDivision' => 'cu_regn',
                        'PostalCode' => '54321',
                        'Country' => 'usa',
                    ],
                ],
                'Product' => [
                    'productClass' => 'CLASS-2',
                    '_' => 'CODE-27890123456789012345678901234567890',
                ],
                'UnitPrice' => 4,
                'Quantity' => 2,
                'ExtendedPrice' => 8,
            ],
            $queryItems[1]
        );
    }

    /**
     * @covers \Vertex\Tax\Model\RequestItem::getFormattedItems()
     */
    public function testAddItemsDeliveryTerm()
    {
        $testItems = [
            'k1' => [
                'product_class' => 'CLASS-1',
                'product_code' => 'CODE-1',
                'price' => 5,
                'qty' => 1,
                'extended_price' => 5,
            ],
            'k2' => [
                'product_class' => 'CLASS-2',
                'product_code' => 'CODE-2m',
                'price' => 4,
                'qty' => 2,
                'extended_price' => 8,
            ],
        ];

        $requestItem = $this->getObject(RequestItem::class);
        $requestItem->setCustomerCountry('CAN');

        $result = $requestItem->getFormattedItems($testItems);
        $this->assertEquals('SUP', $result[0]['deliveryTerm']);
        $this->assertEquals('SUP', $result[1]['deliveryTerm']);
    }
}
