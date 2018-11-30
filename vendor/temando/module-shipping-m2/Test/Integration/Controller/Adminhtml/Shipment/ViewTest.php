<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Shipment;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Temando\Shipping\Model\ResourceModel\Shipment\ShipmentReferenceRepository;
use Temando\Shipping\Test\Integration\Fixture\ShippedOrderFixture;

/**
 * @magentoAppArea adminhtml
 */
class ViewTest extends AbstractBackendController
{
    /**
     * The resource used to authorize action
     *
     * @var string
     */
    protected $resource = 'Temando_Shipping::shipping';

    /**
     * The uri at which to access the controller
     *
     * @var string
     */
    protected $uri = 'backend/temando/shipment/view';

    /**
     * @var ShipmentReferenceRepository
     */
    private $shipmentReferenceRepository;

    /**
     * delegate fixtures creation to separate class.
     */
    public static function createOrderAndShipmentFixture()
    {
        ShippedOrderFixture::createOrderAndShipmentFixture();
    }

    /**
     * delegate fixtures rollback to separate class.
     */
    public static function createOrderAndShipmentFixtureRollback()
    {
        ShippedOrderFixture::createOrderAndShipmentFixtureRollback();
    }

    protected function setUp()
    {
        parent::setUp();

        $this->shipmentReferenceRepository = Bootstrap::getObjectManager()->get(ShipmentReferenceRepository::class);
    }

    /**
     * @test
     * @magentoDataFixture createOrderAndShipmentFixture
     */
    public function shipmentNotFound()
    {
        $shipmentReferenceData = ShippedOrderFixture::getShipmentReferenceData();
        $extShipmentId = $shipmentReferenceData['id'];

        // not existing external shipment id
        $extShipmentId.= '123';
        $this->getRequest()->setParam('shipment_id', $extShipmentId);

        $this->dispatch($this->uri);
        $this->assertEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertEquals(404, $this->getResponse()->getHttpResponseCode());
    }

    /**
     * @test
     * @magentoDataFixture createOrderAndShipmentFixture
     */
    public function redirectSuccess()
    {
        $shipmentReferenceData = ShippedOrderFixture::getShipmentReferenceData();
        $extShipmentId = $shipmentReferenceData['id'];

        $shipmentReference = $this->shipmentReferenceRepository->getByExtShipmentId($extShipmentId);
        $shipmentId = $shipmentReference->getShipmentId();

        // existing external shipment id
        $this->getRequest()->setParam('shipment_id', $extShipmentId);

        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('sales/shipment/view/shipment_id/' . $shipmentId));
    }

    /**
     * @test
     * @magentoDataFixture createOrderAndShipmentFixture
     */
    public function testAclHasAccess()
    {
        $shipmentReferenceData = ShippedOrderFixture::getShipmentReferenceData();
        $extShipmentId = $shipmentReferenceData['id'];

        // existing external shipment id
        $this->getRequest()->setParam('shipment_id', $extShipmentId);

        parent::testAclHasAccess();
    }
}
