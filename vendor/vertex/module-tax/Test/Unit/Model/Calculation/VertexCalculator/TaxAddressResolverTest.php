<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Calculation\VertexCalculator;

use Vertex\Tax\Test\Unit\TestCase;
use Vertex\Tax\Model\Calculation\VertexCalculator\TaxAddressResolver;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Customer\Api\Data\RegionInterface;

/**
 * Test cases for the tax address resolver.
 */
class TaxAddressResolverTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|AccountManagementInterface */
    private $accountManagementMock;

    /** @var TaxAddressResolver */
    private $taxAddressResolverMock;

    /**
     * Initial test environment setup.
     */
    public function setUp()
    {
        parent::setUp();

        $this->accountManagementMock = $this->createMock(AccountManagementInterface::class);
        $this->taxAddressResolverMock = $this->getObject(
            TaxAddressResolver::class,
            ['customerAccountManagement' => $this->accountManagementMock]
        );
    }

    /**
     * Test resolution path to one of the given inputs when the given validates.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\TaxAddressResolver::resolve
     */
    public function testResolveToGivenAddress()
    {
        $billingAddress = $this->getAddressMock(true);
        $shippingAddress = $this->getAddressMock(true);
        $resultAddress = $this->taxAddressResolverMock->resolve(
            $billingAddress,
            $shippingAddress,
            false,
            1
        );

        $this->assertSame($shippingAddress, $resultAddress);
    }

    /**
     * Test resolution path to a fallback on the given inputs when the given do not validate.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\TaxAddressResolver::resolve
     */
    public function testResolveToFallbackAddress()
    {
        $billingAddress = $this->getAddressMock(false);
        $defaultBillingAddress = $this->getAddressMock(true);
        $shippingAddress = $this->getAddressMock(true);
        $customerId = 1;

        $this->accountManagementMock->expects($this->once())
            ->method('getDefaultBillingAddress')
            ->with($customerId)
            ->willReturn($defaultBillingAddress);

        $resultAddress = $this->taxAddressResolverMock->resolve(
            $billingAddress,
            $shippingAddress,
            true,
            $customerId
        );

        $this->assertNotSame($billingAddress, $resultAddress);
    }

    /**
     * Test integrity of a valid address.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\TaxAddressResolver::validate
     */
    public function testAddressDataValidationAsValid()
    {
        $addressMock = $this->getAddressMock(true);

        $this->assertTrue(
            $this->invokeInaccessibleMethod(
                $this->taxAddressResolverMock,
                'validate',
                $addressMock
            )
        );
    }

    /**
     * Test integrity of an invalid address.
     *
     * @covers \Vertex\Tax\Model\Calculation\VertexCalculator\TaxAddressResolver::validate
     */
    public function testAddressDataValidationAsInvalid()
    {
        $addressMock = $this->getAddressMock(false);

        $this->assertFalse(
            $this->invokeInaccessibleMethod(
                $this->taxAddressResolverMock,
                'validate',
                $addressMock
            )
        );
    }

    /**
     * Generate a mock address.
     *
     * @param bool $valid Optionally specify address data integrity.
     * @return \PHPUnit_Framework_MockObject_MockObject|QuoteAddressInterface
     */
    private function getAddressMock($valid = true)
    {
        $mock = $this->getMockBuilder(QuoteAddressInterface::class)
            ->setMethods([])
            ->getMock();

        $data = [
            'country_id' => $valid ? 'US' : null,
            'region_id' => $valid ? '43' : null,
            'region' => $valid ? $this->createMock(RegionInterface::class) : null,
            'postcode' => $valid ? '14201' : null,
        ];

        $mock->method('getCountryId')
            ->willReturn($data['country_id']);
        $mock->method('getRegionId')
            ->willReturn($data['region_id']);
        $mock->method('getRegion')
            ->willReturn($data['region']);
        $mock->method('getPostcode')
            ->willReturn($data['postcode']);

        return $mock;
    }
}
