<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Adapter;

use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\OrderRequestInterface;
use Temando\Shipping\Rest\Response\AllocateOrderInterface;
use Temando\Shipping\Rest\Response\CreateOrderInterface;
use Temando\Shipping\Rest\Response\GetCollectionPointsInterface;
use Temando\Shipping\Rest\Response\UpdateOrderInterface;

/**
 * The Temando Order API interface defines the supported subset of operations
 * as available at the Temando API.
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface OrderApiInterface
{
    const ACTION_CREATE = 'create';
    const ACTION_GET_COLLECTION_POINTS = 'get_collection_points';
    const ACTION_ALLOCATE = 'allocate';
    const ACTION_UPDATE = 'update';

    /**
     * Create order at the platform and retrieve applicable shipping options.
     *
     * For quoting only (if the order is not yet complete/placed) set additional request parameter `persist=false`.
     *
     * @param OrderRequestInterface $request
     * @return CreateOrderInterface
     * @throws AdapterException
     */
    public function createOrder(OrderRequestInterface $request);

    /**
     * Create order at the platform and retrieve applicable collection points.
     *
     * @param OrderRequestInterface $request
     * @return GetCollectionPointsInterface
     * @throws AdapterException
     */
    public function getCollectionPoints(OrderRequestInterface $request);

    /**
     * Manifest order and retrieve allocated shipments.
     *
     * @param OrderRequestInterface $request
     * @return AllocateOrderInterface
     * @throws AdapterException
     */
    public function allocateOrder(OrderRequestInterface $request);

    /**
     * Update order.
     *
     * @param OrderRequestInterface $request
     * @return UpdateOrderInterface
     * @throws AdapterException
     */
    public function updateOrder(OrderRequestInterface $request);
}
