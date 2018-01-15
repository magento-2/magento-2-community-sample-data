<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Config;

use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Temando Config Model Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ModuleConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Config\Storage\Writer | \PHPUnit_Framework_MockObject_MockObject
     */
    private $configWriterMock;

    /**
     * Init object manager
     */
    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();

        $this->configWriterMock = $this->getMockBuilder(\Magento\Framework\App\Config\Storage\Writer::class)
            ->setMethods(['save', 'delete'])
            ->disableOriginalConstructor()
            ->getMock();

        $configAccessor = Bootstrap::getObjectManager()->create(ConfigAccessor::class, [
            'configWriter' => $this->configWriterMock,
        ]);

        $this->config = $this->objectManager->create(ModuleConfig::class, [
            'configAccessor' => $configAccessor,
        ]);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/logging_enabled 1
     */
    public function logIsEnabled()
    {
        $this->assertTrue($this->config->isLoggingEnabled());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/logging_enabled 0
     */
    public function logIsDisabled()
    {
        $this->assertFalse($this->config->isLoggingEnabled());
    }

    /**
     * @test
     * @magentoConfigFixture default/general/store_information/name Info Name
     * @magentoConfigFixture default/general/store_information/phone Info Phone
     * @magentoConfigFixture default/general/store_information/country_id Info Contry
     * @magentoConfigFixture default/general/store_information/postcode Info Postcode
     * @magentoConfigFixture default/general/store_information/city Info City
     * @magentoConfigFixture default/general/store_information/street_line1 Info Street
     */
    public function getStoreInformation()
    {
        $info = $this->config->getStoreInformation();

        $this->assertInstanceOf(\Magento\Framework\DataObject::class, $info);
        $this->assertEquals('Info Name', $info->getData('name'));
        $this->assertEquals('Info Phone', $info->getData('phone'));
        $this->assertEquals('Info Contry', $info->getData('country_id'));
        $this->assertEquals('Info Postcode', $info->getData('postcode'));
        $this->assertEquals('Info City', $info->getData('city'));
        $this->assertEquals('Info Street', $info->getData('street_line1'));
    }

    /**
     * @test
     * @magentoConfigFixture default/shipping/origin/postcode Origin Postcode
     * @magentoConfigFixture default/shipping/origin/city Origin City
     * @magentoConfigFixture default/shipping/origin/street_line1 Origin Street
     */
    public function getShippingOrigin()
    {
        $origin = $this->config->getShippingOrigin();

        $this->assertInstanceOf(\Magento\Framework\DataObject::class, $origin);
        $this->assertEquals('Origin Postcode', $origin->getData('postcode'));
        $this->assertEquals('Origin City', $origin->getData('city'));
        $this->assertEquals('Origin Street', $origin->getData('street_line1'));
    }

    /**
     * @test
     * @magentoConfigFixture default/general/locale/weight_unit Foo Unit
     */
    public function getWeightUnit()
    {
        $this->assertEquals('Foo Unit', $this->config->getWeightUnit());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint endpoint.com
     */
    public function getApiEndpoint()
    {
        $this->assertEquals('endpoint.com', $this->config->getApiEndpoint());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     */
    public function getAccountId()
    {
        $this->assertEquals('23', $this->config->getAccountId());
    }

    /**
     * @test
     */
    public function saveAccountId()
    {
        $this->configWriterMock
            ->expects($this->once())
            ->method('save')
            ->with(ModuleConfig::CONFIG_XML_PATH_ACCOUNT_ID, '12');

        $this->config->saveAccountId('12');
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function getBearerToken()
    {
        $this->assertEquals('808', $this->config->getBearerToken());
    }

    /**
     * @test
     */
    public function saveBearerToken()
    {
        $this->configWriterMock
            ->expects($this->once())
            ->method('save')
            ->with(ModuleConfig::CONFIG_XML_PATH_BEARER_TOKEN, 'bearerToken');

        $this->config->saveBearerToken('bearerToken');
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id accountId
     * @magentoConfigFixture default/carriers/temando/bearer_token bearerToken
     */
    public function credentialsAreAvailable()
    {
        $this->assertTrue($this->config->isRegistered());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/bearer_token bearerToken
     */
    public function credentialsAccountMissing()
    {
        $this->assertFalse($this->config->isRegistered());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id accountId
     */
    public function credentialsBearerTokenMissing()
    {
        $this->assertFalse($this->config->isRegistered());
    }

    /**
     * @test
     */
    public function credentialsAreNotAvailable()
    {
        $this->assertFalse($this->config->isRegistered());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/bearer_token_expiry 123456
     */
    public function getBearerTokenExpiry()
    {
        $this->assertEquals('123456', $this->config->getBearerTokenExpiry());
    }

    /**
     * @test
     */
    public function saveBearerTokenExpiry()
    {
        $this->configWriterMock
            ->expects($this->once())
            ->method('save')
            ->with(ModuleConfig::CONFIG_XML_PATH_BEARER_TOKEN_EXPIRY, '123456');

        $this->config->saveBearerTokenExpiry('123456');
    }

    /**
     * @test
     */
    public function setAccount()
    {
        $this->configWriterMock
            ->expects($this->exactly(3))
            ->method('save')
            ->withConsecutive(
                ['carriers/temando/account_id', '12'],
                ['carriers/temando/bearer_token', 'bearerToken'],
                ['carriers/temando/bearer_token_expiry', '123456']
            );

        $this->config->setAccount('12', 'bearerToken', '123456');
    }

    /**
     * @test
     */
    public function unsetAccount()
    {
        $this->configWriterMock
            ->expects($this->exactly(3))
            ->method('delete')
            ->withConsecutive(
                ['carriers/temando/account_id'],
                ['carriers/temando/bearer_token'],
                ['carriers/temando/bearer_token_expiry']
            );

        $this->config->unsetAccount();
    }
}
