<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Get Stream Events Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class GetStreamEvents implements GetStreamEventsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\StreamEventResponseType[]
     */
    private $data = [];

    /**
     * @return \Temando\Shipping\Rest\Response\Type\StreamEventResponseType[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\StreamEventResponseType[] $events
     * @return void
     */
    public function setData(array $events)
    {
        $this->data = $events;
    }
}
