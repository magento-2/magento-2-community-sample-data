<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Shipment;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Test\Integration\Provider\RestResponseProvider;
use Temando\Shipping\Webservice\HttpClientInterfaceFactory;

/**
 * Temando Shipment Repository Test
 *
 * @magentoAppIsolation enabled
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function getShipmentResponseDataProvider()
    {
        return RestResponseProvider::getShipmentResponseDataProvider();
    }

    /**
     * Set valid session token
     */
    protected function setUp()
    {
        parent::setUp();

        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->setData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY, '2038-01-19T03:03:33.000Z');
    }

    protected function tearDown()
    {
        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->unsetData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY);

        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider getShipmentResponseDataProvider
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     * @param string $responseBody
     */
    public function getById($responseBody)
    {
        $shipmentId = '00000000-5000-0005-0000-000000000000';

        $testResponse = new \Zend\Http\Response();
        $testResponse->setStatusCode(\Zend\Http\Response::STATUS_CODE_200);
        $testResponse->setContent($responseBody);

        $testAdapter = new \Zend\Http\Client\Adapter\Test();
        $testAdapter->setResponse($testResponse);

        $zendClient = new \Zend\Http\Client();
        $zendClient->setAdapter($testAdapter);

        $httpClient = Bootstrap::getObjectManager()->create(\Temando\Shipping\Webservice\HttpClient::class, [
            'client' => $zendClient,
        ]);

        $clientFactoryMock = $this->getMockBuilder(HttpClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($httpClient);
        Bootstrap::getObjectManager()->addSharedInstance($clientFactoryMock, HttpClientInterfaceFactory::class);

        /** @var ShipmentRepositoryInterface $shipmentRepository */
        $shipmentRepository = Bootstrap::getObjectManager()->get(ShipmentRepositoryInterface::class);
        $shipment = $shipmentRepository->getById($shipmentId);
        $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        $this->assertEquals($shipmentId, $shipment->getShipmentId());

        $documentation = $shipment->getDocumentation();
        $this->assertInternalType('array', $documentation);
        $this->assertNotEmpty($documentation);
        $this->assertCount(2, $documentation);
    }
}
