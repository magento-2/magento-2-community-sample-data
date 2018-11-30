<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Adapter;

use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\FulfillmentRequestInterface;
use Temando\Shipping\Rest\Request\ItemRequestInterface;
use Temando\Shipping\Rest\Request\ListRequestInterface;
use Temando\Shipping\Rest\Response\DataObject\Fulfillment;

/**
 * The Temando Fulfillment API interface defines the supported subset of operations
 * as available at the Temando API.
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface FulfillmentApiInterface
{
    /**
     * @param ItemRequestInterface $request
     *
     * @return Fulfillment
     * @throws AdapterException
     */
    public function getFulfillment(ItemRequestInterface $request);

    /**
     * @param ListRequestInterface $request
     *
     * @return Fulfillment[]
     * @throws AdapterException
     */
    public function getFulfillments(ListRequestInterface $request);

    /**
     * Create fulfillment at the platform.
     *
     * @param FulfillmentRequestInterface $request
     *
     * @return Fulfillment
     * @throws AdapterException
     */
    public function createFulfillment(FulfillmentRequestInterface $request);

    /**
     * Update fulfillment at the platform.
     *
     * @param FulfillmentRequestInterface $request
     *
     * @return Fulfillment
     * @throws AdapterException
     */
    public function updateFulfillment(FulfillmentRequestInterface $request);
}
