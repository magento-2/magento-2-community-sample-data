<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\ViewModel\Carrier;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use Temando\Shipping\Model\Carrier;
use Temando\Shipping\Model\CarrierInterface;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * Temando Carrier View Model Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierViewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return CarrierInterface
     */
    private function getCarrier()
    {
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class, ['data' => [
            CarrierInterface::NAME => 'Foo Carrier',
            CarrierInterface::CONFIGURATION_ID => '00000000-5000-0005-0000-000000000000',
            CarrierInterface::INTEGRATION_ID => 'carrier-integration-foo',
        ]]);

        return $carrier;
    }

    /**
     * @test
     */
    public function backButtonIsAvailableInEditComponents()
    {
        /** @var CarrierEdit $carrierEdit */
        $carrierEdit = Bootstrap::getObjectManager()->get(CarrierEdit::class);
        $this->assertInstanceOf(PageActionsInterface::class, $carrierEdit);

        $actions = $carrierEdit->getMainActions();

        $this->assertNotEmpty($actions);
        $this->assertInternalType('array', $actions);
        $this->assertArrayHasKey('back', $actions);
    }

    /**
     * @test
     */
    public function backButtonIsNotAvailableInRegistrationComponents()
    {
        /** @var CarrierRegistration $carrierRegistration */
        $carrierRegistration = Bootstrap::getObjectManager()->get(CarrierRegistration::class);
        $this->assertNotInstanceOf(PageActionsInterface::class, $carrierRegistration);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     */
    public function shippingApiCredentialsAreAvailableInCarrierComponents()
    {
        /** @var CarrierEdit $carrierEdit */
        $carrierEdit = Bootstrap::getObjectManager()->get(CarrierEdit::class);
        $this->assertInstanceOf(ShippingApiInterface::class, $carrierEdit);
        $this->assertEquals('https://foo.temando.io/v1/', $carrierEdit->getShippingApiAccess()->getApiEndpoint());

        /** @var CarrierRegistration $carrierRegistration */
        $carrierRegistration = Bootstrap::getObjectManager()->get(CarrierRegistration::class);
        $this->assertInstanceOf(ShippingApiInterface::class, $carrierRegistration);
        $this->assertEquals(
            'https://foo.temando.io/v1/',
            $carrierRegistration->getShippingApiAccess()->getApiEndpoint()
        );
    }

    /**
     * @test
     */
    public function carrierIdsAreAvailableInEditComponent()
    {
        $carrier = $this->getCarrier();

        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $request
            ->expects($this->exactly(2))
            ->method('getParam')
            ->withConsecutive(
                [CarrierInterface::CONFIGURATION_ID, null],
                [CarrierInterface::INTEGRATION_ID, null]
            )
            ->willReturnOnConsecutiveCalls($carrier->getConfigurationId(), $carrier->getIntegrationId());

        /** @var CarrierEdit $carrierEdit */
        $carrierEdit = Bootstrap::getObjectManager()->create(CarrierEdit::class, [
            'request' => $request,
        ]);
        $this->assertEquals($carrier->getConfigurationId(), $carrierEdit->getCarrierConfigurationId());
        $this->assertEquals($carrier->getIntegrationId(), $carrierEdit->getCarrierIntegrationId());
    }

    /**
     * @test
     */
    public function carrierIntegrationIdIsAvailableInRegistrationComponent()
    {
        $carrier = $this->getCarrier();

        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $request
            ->expects($this->once())
            ->method('getParam')
            ->with(CarrierInterface::INTEGRATION_ID)
            ->willReturn($carrier->getIntegrationId());

        /** @var CarrierRegistration $carrierRegistration */
        $carrierRegistration = Bootstrap::getObjectManager()->create(CarrierRegistration::class, [
            'request' => $request,
        ]);
        $this->assertEquals($carrier->getIntegrationId(), $carrierRegistration->getCarrierIntegrationId());
    }

    /**
     * @test
     */
    public function entityUrlsAreAvailableInCarrierComponents()
    {
        /** @var Carrier $carrier */
        $carrier = $this->getCarrier();

        /** @var CarrierEdit $carrierEdit */
        $carrierEdit = Bootstrap::getObjectManager()->get(CarrierEdit::class);
        $this->assertInstanceOf(EntityUrlInterface::class, $carrierEdit->getCarrierUrl());

        /** @var CarrierRegistration $carrierRegistration */
        $carrierRegistration = Bootstrap::getObjectManager()->get(CarrierRegistration::class);
        $this->assertInstanceOf(EntityUrlInterface::class, $carrierRegistration->getCarrierUrl());

        // application does not provide view action for carriers
        $this->assertEmpty($carrierRegistration->getCarrierUrl()->getViewActionUrl($carrier->getData()));
        $this->assertContains('new', $carrierRegistration->getCarrierUrl()->getNewActionUrl());
        $this->assertContains('index', $carrierRegistration->getCarrierUrl()->getListActionUrl());
        $this->assertContains('register', $carrierRegistration->getCarrierUrl()->getCarrierRegistrationPageUrl());

        $editUrl = $carrierRegistration->getCarrierUrl()->getEditActionUrl($carrier->getData());
        $this->assertContains('edit', $editUrl);
        $this->assertContains($carrier->getConfigurationId(), $editUrl);
        $this->assertContains($carrier->getIntegrationId(), $editUrl);

        $deleteUrl = $carrierRegistration->getCarrierUrl()->getDeleteActionUrl($carrier->getData());
        $this->assertContains('delete', $deleteUrl);
        $this->assertContains($carrier->getConfigurationId(), $deleteUrl);
        $this->assertNotContains($carrier->getIntegrationId(), $deleteUrl);
    }

    /**
     * @test
     */
    public function maliciousParamValuesGetStripped()
    {
        $badIntegrationId = '<script>alert("integration");</script>';
        $badConfigId = '<script>alert("config");</script>';

        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $request
            ->expects($this->exactly(3))
            ->method('getParam')
            ->withConsecutive(
                [CarrierInterface::INTEGRATION_ID, null],
                [CarrierInterface::INTEGRATION_ID, null],
                [CarrierInterface::CONFIGURATION_ID, null]
            )
            ->willReturnOnConsecutiveCalls($badIntegrationId, $badIntegrationId, $badConfigId);

        /** @var CarrierRegistration $carrierRegistration */
        $carrierRegistration = Bootstrap::getObjectManager()->create(CarrierRegistration::class, [
            'request' => $request,
        ]);
        $this->assertRegExp('/^[\w0-9-_]+$/', $carrierRegistration->getCarrierIntegrationId());

        /** @var CarrierEdit $carrierEdit */
        $carrierEdit = Bootstrap::getObjectManager()->create(CarrierEdit::class, [
            'request' => $request,
        ]);
        $this->assertRegExp('/^[\w0-9-_]+$/', $carrierEdit->getCarrierIntegrationId());
        $this->assertRegExp('/^[\w0-9-_]+$/', $carrierEdit->getCarrierConfigurationId());
    }
}
