<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model;

use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;

/**
 * Temando Shipment Reference Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentReferenceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Init object manager
     */
    public function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @test
     */
    public function dataIsSetThroughConstructorArgument()
    {
        $entityId = 303;
        $shipmentId = 808;
        $shipmentReferenceId = 'F00-S01';
        $locationReferenceId = 'F00-L01';
        $trackingReferenceId = 'F00-T01';
        $trackingUrl = 'https://example.org/';

        /** @var ShipmentReferenceInterface $shipmentReference */
        $shipmentReference = $this->objectManager->create(ShipmentReferenceInterface::class, ['data' => [
            ShipmentReferenceInterface::ENTITY_ID => $entityId,
            ShipmentReferenceInterface::SHIPMENT_ID => $shipmentId,
            ShipmentReferenceInterface::EXT_SHIPMENT_ID => $shipmentReferenceId,
            ShipmentReferenceInterface::EXT_LOCATION_ID => $locationReferenceId,
            ShipmentReferenceInterface::EXT_TRACKING_REFERENCE => $trackingReferenceId,
            ShipmentReferenceInterface::EXT_TRACKING_URL => $trackingUrl,
        ]]);

        $this->assertEquals($entityId, $shipmentReference->getEntityId());
        $this->assertEquals($shipmentId, $shipmentReference->getShipmentId());
        $this->assertEquals($shipmentReferenceId, $shipmentReference->getExtShipmentId());
        $this->assertEquals($locationReferenceId, $shipmentReference->getExtLocationId());
        $this->assertEquals($trackingReferenceId, $shipmentReference->getExtTrackingReference());
        $this->assertEquals($trackingUrl, $shipmentReference->getExtTrackingUrl());
    }

    /**
     * @test
     */
    public function dataIsSetThroughSetters()
    {
        $entityId = 303;
        $shipmentId = 808;
        $shipmentReferenceId = 'F00-S01';
        $locationReferenceId = 'F00-L01';
        $trackingReferenceId = 'F00-T01';
        $trackingUrl = 'https://example.org/';

        /** @var ShipmentReferenceInterface $shipmentReference */
        $shipmentReference = $this->objectManager->create(ShipmentReferenceInterface::class);
        $this->assertEmpty($shipmentReference->getEntityId());

        $shipmentReference->setEntityId($entityId);
        $this->assertEquals($entityId, $shipmentReference->getEntityId());

        $shipmentReference->setShipmentId($shipmentId);
        $this->assertEquals($shipmentId, $shipmentReference->getShipmentId());

        $shipmentReference->setExtShipmentId($shipmentReferenceId);
        $this->assertEquals($shipmentReferenceId, $shipmentReference->getExtShipmentId());

        $shipmentReference->setExtLocationId($locationReferenceId);
        $this->assertEquals($locationReferenceId, $shipmentReference->getExtLocationId());

        $shipmentReference->setExtTrackingReference($trackingReferenceId);
        $this->assertEquals($trackingReferenceId, $shipmentReference->getExtTrackingReference());

        $shipmentReference->setExtTrackingUrl($trackingUrl);
        $this->assertEquals($trackingUrl, $shipmentReference->getExtTrackingUrl());
    }
}
