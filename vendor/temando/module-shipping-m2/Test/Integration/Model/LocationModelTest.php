<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Temando Location Model Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class LocationModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function dataIsSetThroughConstructorArgument()
    {
        $locationId = 'eebf51a8-b4b8-4ee2-b830-e0f8c9a3c559';
        $name = 'Foo Store';
        $uniqueIdentifier = 'foo-store';
        $type = 'Store';
        $street = '77 Foo Street';
        $postalCode = '4006';
        $isDefault = true;

        /** @var Location $location */
        $location = Bootstrap::getObjectManager()->create(Location::class, ['data' => [
            Location::LOCATION_ID => $locationId,
            Location::NAME => $name,
            Location::UNIQUE_IDENTIFIER => $uniqueIdentifier,
            Location::TYPE => $type,
            Location::STREET => $street,
            Location::POSTAL_CODE => $postalCode,
            Location::IS_DEFAULT => $isDefault,
        ]]);

        $this->assertEquals($locationId, $location->getLocationId());
        $this->assertEquals($name, $location->getName());
        $this->assertEquals($uniqueIdentifier, $location->getUniqueIdentifier());
        $this->assertEquals($type, $location->getType());
        $this->assertEquals($street, $location->getStreet());
        $this->assertEquals($postalCode, $location->getPostalCode());
        $this->assertEquals($isDefault, $location->isDefault());
    }

    /**
     * @test
     */
    public function dataIsSetThroughSetters()
    {
        $locationId = 'eebf51a8-b4b8-4ee2-b830-e0f8c9a3c559';
        $name = 'Foo Store';
        $uniqueIdentifier = 'foo-store';
        $type = 'Store';
        $street = '77 Foo Street';
        $postalCode = '4006';
        $isDefault = true;

        /** @var Location $location */
        $location = Bootstrap::getObjectManager()->create(Location::class);

        $this->assertEmpty($location->getLocationId());

        $location->setData(Location::LOCATION_ID, $locationId);
        $this->assertEquals($locationId, $location->getLocationId());

        $location->setData(Location::NAME, $name);
        $this->assertEquals($name, $location->getName());

        $location->setData(Location::UNIQUE_IDENTIFIER, $uniqueIdentifier);
        $this->assertEquals($uniqueIdentifier, $location->getUniqueIdentifier());

        $location->setData(Location::TYPE, $type);
        $this->assertEquals($type, $location->getType());

        $location->setData(Location::STREET, $street);
        $this->assertEquals($street, $location->getStreet());

        $location->setData(Location::POSTAL_CODE, $postalCode);
        $this->assertEquals($postalCode, $location->getPostalCode());

        $location->setData(Location::IS_DEFAULT, $isDefault);
        $this->assertEquals($isDefault, $location->isDefault());
    }
}
