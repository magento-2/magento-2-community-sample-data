<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Adapter;

use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterface;
use Temando\Shipping\Rest\Request\StreamEventItemRequestInterface;
use Temando\Shipping\Rest\Request\StreamEventListRequestInterface;
use Temando\Shipping\Rest\Request\StreamCreateRequestInterface;
use Temando\Shipping\Rest\Response\Type\StreamEventResponseType;

/**
 * Temando API Adapter Event Stream Part
 *
 * @package  Temando\Shipping\Rest
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface EventStreamApiInterface
{
    /**
     * @param StreamCreateRequestInterface $request
     *
     * @return void
     * @throws AdapterException
     */
    public function createStream(StreamCreateRequestInterface $request);

    /**
     * @param ItemRequestInterface $request
     *
     * @return void
     * @throws AdapterException
     */
    public function deleteStream(ItemRequestInterface $request);

    /**
     * @param StreamEventListRequestInterface $request
     *
     * @return StreamEventResponseType[]
     * @throws AdapterException
     */
    public function getStreamEvents(StreamEventListRequestInterface $request);

    /**
     * @param StreamEventItemRequestInterface $request
     *
     * @return void
     * @throws AdapterException
     */
    public function deleteStreamEvent(StreamEventItemRequestInterface $request);
}
