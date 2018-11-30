<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Get Tracking Event Collection Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetTrackingEvents implements GetTrackingEventsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\TrackingEvent[]
     */
    private $data = [];

    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\TrackingEvent[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $tracking \Temando\Shipping\Rest\Response\DataObject\TrackingEvent[]
     * @return void
     */
    public function setData(array $tracking)
    {
        $this->data = $tracking;
    }
}
