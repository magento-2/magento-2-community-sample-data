<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Shipment;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Temando\Shipping\Model\Shipment\Fulfillment;
use Temando\Shipping\Model\Shipment\FulfillmentInterface;

class FulfillmentTest extends \PHPUnit\Framework\TestCase
{
    /** @var ObjectManager $objectManager */
    private $objectManager;
    /** @var Fulfillment $fulfillment */
    private $fulfillment;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->fulfillment = $this->objectManager->create(Fulfillment::class);
        $this->fulfillment->setData(FulfillmentInterface::SERVICE_NAME, 'serviceName');
        $this->fulfillment->setData(FulfillmentInterface::TRACKING_REFERENCE, 'trackingReference');
    }

    /**
     * @test
     */
    public function getServiceNameTest()
    {
        $result = $this->fulfillment->getServiceName();
        $this->assertEquals($result, "serviceName");
    }

    /**
     * @test
     */
    public function getTrackingReferenceTest()
    {
        $result = $this->fulfillment->getTrackingReference();
        $this->assertEquals($result, "trackingReference");
    }
}
