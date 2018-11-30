<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\ViewModel\Location;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use Temando\Shipping\Model\Location;
use Temando\Shipping\Model\LocationInterface;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * Temando Location View Model Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class LocationViewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return LocationInterface
     */
    private function getLocation()
    {
        $location = Bootstrap::getObjectManager()->create(Location::class, ['data' => [
            LocationInterface::NAME => 'Foo Location',
            LocationInterface::LOCATION_ID => '00000000-6000-0006-0000-000000000000',
        ]]);

        return $location;
    }

    /**
     * @test
     */
    public function backButtonIsAvailableInEditComponents()
    {
        /** @var LocationEdit $locationEdit */
        $locationEdit = Bootstrap::getObjectManager()->get(LocationEdit::class);
        $this->assertInstanceOf(PageActionsInterface::class, $locationEdit);

        $actions = $locationEdit->getMainActions();

        $this->assertNotEmpty($actions);
        $this->assertInternalType('array', $actions);
        $this->assertArrayHasKey('back', $actions);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     */
    public function shippingApiCredentialsAreAvailableInLocationComponents()
    {
        /** @var LocationEdit $locationEdit */
        $locationEdit = Bootstrap::getObjectManager()->get(LocationEdit::class);
        $this->assertInstanceOf(ShippingApiInterface::class, $locationEdit);
        $this->assertEquals('https://foo.temando.io/v1/', $locationEdit->getShippingApiAccess()->getApiEndpoint());
    }

    /**
     * @test
     */
    public function locationIdIsAvailableInEditComponent()
    {
        $location = $this->getLocation();

        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $request
            ->expects($this->exactly(1))
            ->method('getParam')
            ->with(LocationInterface::LOCATION_ID)
            ->willReturn($location->getLocationId());

        /** @var LocationEdit $locationEdit */
        $locationEdit = Bootstrap::getObjectManager()->create(LocationEdit::class, [
            'request' => $request,
        ]);
        $this->assertEquals($location->getLocationId(), $locationEdit->getLocationId());
    }

    /**
     * @test
     */
    public function entityUrlsAreAvailableInLocationComponents()
    {
        /** @var Location $location */
        $location = $this->getLocation();

        /** @var LocationEdit $locationEdit */
        $locationEdit = Bootstrap::getObjectManager()->get(LocationEdit::class);
        $this->assertInstanceOf(EntityUrlInterface::class, $locationEdit->getLocationUrl());

        // application does not provide view action for locations
        $this->assertEmpty($locationEdit->getLocationUrl()->getViewActionUrl($location->getData()));
        $this->assertContains('new', $locationEdit->getLocationUrl()->getNewActionUrl());
        $this->assertContains('index', $locationEdit->getLocationUrl()->getListActionUrl());

        $editUrl = $locationEdit->getLocationUrl()->getEditActionUrl($location->getData());
        $this->assertContains('edit', $editUrl);
        $this->assertContains($location->getLocationId(), $editUrl);

        $deleteUrl = $locationEdit->getLocationUrl()->getDeleteActionUrl($location->getData());
        $this->assertContains('delete', $deleteUrl);
        $this->assertContains($location->getLocationId(), $deleteUrl);
    }

    /**
     * @test
     */
    public function maliciousParamValuesGetStripped()
    {
        $badLocationId = '<script>alert("location");</script>';

        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $request
            ->expects($this->exactly(1))
            ->method('getParam')
            ->with(LocationInterface::LOCATION_ID)
            ->willReturn($badLocationId);

        /** @var LocationEdit $locationEdit */
        $locationEdit = Bootstrap::getObjectManager()->create(LocationEdit::class, [
            'request' => $request,
        ]);
        $this->assertRegExp('/^[\w0-9-_]+$/', $locationEdit->getLocationId());
    }
}
