<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\DataObject;

/**
 * Temando Track Event Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class TrackEvent extends DataObject implements TrackEventInterface
{
    /**
     * @return string
     */
    public function getTrackingEventId()
    {
        return $this->getData(TrackEventInterface::TRACKING_EVENT_ID);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(TrackEventInterface::STATUS);
    }

    /**
     * @return string
     */
    public function getOccurredAt()
    {
        return $this->getData(TrackEventInterface::OCCURRED_AT);
    }

    /**
     * @return string[]
     */
    public function getEventData()
    {
        return [
            'deliverydate' => date('Y-m-d', strtotime($this->getOccurredAt())),
            'deliverytime' => date('H:i:s', strtotime($this->getOccurredAt())),
            'deliverylocation' => __('Not Available'),
            'activity' => $this->getStatus(),
        ];
    }
}
