<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Get Tracking Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class GetTrackingEvents implements GetTrackingEventsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\TrackingEventResponseType[]
     */
    private $data = [];

    /**
     * Obtain response entity
     *
     * @return \Temando\Shipping\Rest\Response\Type\TrackingEventResponseType[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entity
     *
     * @param $tracking \Temando\Shipping\Rest\Response\Type\TrackingEventResponseType[]
     * @return void
     */
    public function setData(array $tracking)
    {
        $this->data = $tracking;
    }
}
