<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Temando Carrier Moodel Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function dataIsSetThroughConstructorArgument()
    {
        $configurationId = 'carrier-ups';
        $integrationId = 'carrier-integration-ups';
        $name = 'United Parcel Services (UPS)';
        $connectionName = 'UPS';
        $status = 'pending';
        $activeServices = ['fast', 'faster', 'express'];
        $logo = 'https://example.com/logo-ups.svg';

        /** @var Carrier $carrier */
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class, ['data' => [
            Carrier::CONFIGURATION_ID => $configurationId,
            Carrier::INTEGRATION_ID => $integrationId,
            Carrier::NAME => $name,
            Carrier::CONNECTION_NAME => $connectionName,
            Carrier::STATUS => $status,
            Carrier::ACTIVE_SERVICES => $activeServices,
            Carrier::LOGO => $logo,
        ]]);

        $this->assertEquals($configurationId, $carrier->getConfigurationId());
        $this->assertEquals($integrationId, $carrier->getIntegrationId());
        $this->assertEquals($name, $carrier->getName());
        $this->assertEquals($connectionName, $carrier->getConnectionName());
        $this->assertEquals($status, $carrier->getStatus());
        $this->assertEquals($activeServices, $carrier->getActiveServices());
        $this->assertEquals($logo, $carrier->getLogo());
    }

    /**
     * @test
     */
    public function dataIsSetThroughSetters()
    {
        $configurationId = 'carrier-ups';
        $integrationId = 'carrier-integration-ups';
        $name = 'United Parcel Services (UPS)';
        $connectionName = 'UPS';
        $status = 'pending';
        $activeServices = ['fast', 'faster', 'express'];
        $logo = 'https://example.com/logo-ups.svg';

        /** @var Carrier $carrier */
        $carrier = Bootstrap::getObjectManager()->create(Carrier::class);

        $this->assertEmpty($carrier->getConfigurationId());

        $carrier->setData(Carrier::CONFIGURATION_ID, $configurationId);
        $this->assertEquals($configurationId, $carrier->getConfigurationId());

        $carrier->setData(Carrier::INTEGRATION_ID, $integrationId);
        $this->assertEquals($integrationId, $carrier->getIntegrationId());

        $carrier->setData(Carrier::NAME, $name);
        $this->assertEquals($name, $carrier->getName());

        $carrier->setData(Carrier::CONNECTION_NAME, $connectionName);
        $this->assertEquals($connectionName, $carrier->getConnectionName());

        $carrier->setData(Carrier::STATUS, $status);
        $this->assertEquals($status, $carrier->getStatus());

        $carrier->setData(Carrier::ACTIVE_SERVICES, $activeServices);
        $this->assertEquals($activeServices, $carrier->getActiveServices());

        $carrier->setData(Carrier::LOGO, $logo);
        $this->assertEquals($logo, $carrier->getLogo());
    }
}
