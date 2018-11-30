<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Shipment;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Temando\Shipping\Model\Shipment\ShipmentProvider;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Magento\Sales\Api\Data\ShipmentInterface as SalesShipmentInterface;
use Temando\Shipping\Model\ShipmentInterface;

class ShipmentProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var ObjectManager $objectManager */
    private $objectManager;
    /** @var ShipmentProvider $shipmentProvider */
    private $shipmentProvider;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->shipmentProvider = $this->objectManager->create(ShipmentProvider::class);
    }

    /**
     * @test
     */
    public function shipmentTest()
    {
        $shipment = $this->objectManager->create(ShipmentInterface::class);
        $this->shipmentProvider->setShipment($shipment);
        $result = $this->shipmentProvider->getShipment();
        $this->assertEquals($result, $shipment);
    }

    /**
     * @test
     */
    public function salesShipmentTest()
    {
        $salesShipment = $this->objectManager->create(SalesShipmentInterface::class);
        $this->shipmentProvider->setSalesShipment($salesShipment);
        $result = $this->shipmentProvider->getSalesShipment();
        $this->assertEquals($result, $salesShipment);
    }
}
