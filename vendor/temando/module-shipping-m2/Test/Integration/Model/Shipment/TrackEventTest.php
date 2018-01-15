<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Shipment;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Temando Shipping Track Event Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class TrackEventTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function getEventData()
    {
        $trackEventId = '7777-2222-a4aa4a-bbbb';
        $status = 'dunno';
        $occurredAt = '2017-10-10T11:11:11Z';

        /** @var TrackEventInterface $trackEvent */
        $trackEvent = Bootstrap::getObjectManager()->create(TrackEventInterface::class, ['data' => [
            TrackEventInterface::TRACKING_EVENT_ID => $trackEventId,
            TrackEventInterface::STATUS => $status,
            TrackEventInterface::OCCURRED_AT => $occurredAt,
        ]]);

        $eventData = $trackEvent->getEventData();
        $this->assertInternalType('array', $eventData);
        $this->assertArrayHasKey('deliverydate', $eventData);
        $this->assertArrayHasKey('deliverytime', $eventData);
        $this->assertArrayHasKey('deliverylocation', $eventData);
        $this->assertArrayHasKey('activity', $eventData);

        $this->assertEquals($status, $eventData['activity']);
        $this->assertRegExp('/^\d{4}-\d{2}-\d{2}$/', $eventData['deliverydate']);
        $this->assertRegExp('/^\d{2}:\d{2}:\d{2}$/', $eventData['deliverytime']);
    }
}
