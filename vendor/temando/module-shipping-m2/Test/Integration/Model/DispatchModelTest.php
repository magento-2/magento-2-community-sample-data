<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Temando Dispatch Model Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function dataIsSetThroughConstructorArgument()
    {
        $dispatchId = '03df240e-969d-4549-bf10-ea7cd22f3070';
        $status = 'processing';
        $carrierName = 'UPS';
        $createdAtDate = '2017-03-03T04:35:42.022Z';
        $readyAtDate = '2017-01-01T00:00:01Z';
        $shipmentCount = 3;
        $documentation = [];

        /** @var Dispatch $dispatch */
        $dispatch = Bootstrap::getObjectManager()->create(Dispatch::class, ['data' => [
            Dispatch::DISPATCH_ID => $dispatchId,
            Dispatch::STATUS => $status,
            Dispatch::CARRIER_NAME => $carrierName,
            Dispatch::CREATED_AT_DATE => $createdAtDate,
            Dispatch::READY_AT_DATE => $readyAtDate,
            Dispatch::INCLUDED_SHIPMENTS => $shipmentCount,
            Dispatch::DOCUMENTATION => $documentation,
        ]]);

        $this->assertEquals($dispatchId, $dispatch->getDispatchId());
        $this->assertEquals($status, $dispatch->getStatus());
        $this->assertEquals($carrierName, $dispatch->getCarrierName());
        $this->assertEquals($createdAtDate, $dispatch->getCreatedAtDate());
        $this->assertEquals($readyAtDate, $dispatch->getReadyAtDate());
        $this->assertEquals($shipmentCount, $dispatch->getIncludedShipments());
        $this->assertEquals($documentation, $dispatch->getDocumentation());
    }

    /**
     * @test
     */
    public function dataIsSetThroughSetters()
    {
        $dispatchId = '03df240e-969d-4549-bf10-ea7cd22f3070';
        $status = 'processing';
        $carrierName = 'UPS';
        $createdAtDate = '2017-03-03T04:35:42.022Z';
        $readyAtDate = '2017-01-01T00:00:01Z';
        $shipmentCount = 3;
        $documentation = [];

        /** @var Dispatch $dispatch */
        $dispatch = Bootstrap::getObjectManager()->create(Dispatch::class);

        $this->assertEmpty($dispatch->getDispatchId());

        $dispatch->setData(Dispatch::DISPATCH_ID, $dispatchId);
        $this->assertEquals($dispatchId, $dispatch->getDispatchId());

        $dispatch->setData(Dispatch::STATUS, $status);
        $this->assertEquals($status, $dispatch->getStatus());

        $dispatch->setData(Dispatch::CARRIER_NAME, $carrierName);
        $this->assertEquals($carrierName, $dispatch->getCarrierName());

        $dispatch->setData(Dispatch::CREATED_AT_DATE, $createdAtDate);
        $this->assertEquals($createdAtDate, $dispatch->getCreatedAtDate());

        $dispatch->setData(Dispatch::READY_AT_DATE, $readyAtDate);
        $this->assertEquals($readyAtDate, $dispatch->getReadyAtDate());

        $dispatch->setData(Dispatch::INCLUDED_SHIPMENTS, $shipmentCount);
        $this->assertEquals($shipmentCount, $dispatch->getIncludedShipments());

        $dispatch->setData(Dispatch::DOCUMENTATION, $documentation);
        $this->assertEquals($documentation, $dispatch->getDocumentation());
    }
}
