<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Adapter;

use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\OrderRequestInterface;
use Temando\Shipping\Rest\Response\Document\AllocateOrderInterface;
use Temando\Shipping\Rest\Response\Document\GetCollectionPointsInterface;
use Temando\Shipping\Rest\Response\Document\QualifyOrderInterface;

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
    /**
     * QUOTE or MANIFEST (depends on persist parameter)
     * → Applicable to regular "ship to address" orders
     */
    const ACTION_CREATE = 'create';

    /**
     * QUOTE or MANIFEST (depends on persist parameter)
     * → Applicable to "click & collect" orders
     */
    const ACTION_CREATE_PICKUP_ORDER = 'create_pickup_order';

    /**
     * QUOTE
     * → Applicable to collection point orders
     */
    const ACTION_GET_COLLECTION_POINTS = 'get_collection_points';

    /**
     * MANIFEST with shipment allocation
     * → Applicable to regular and collection point orders
     */
    const ACTION_ALLOCATE = 'allocate';

    /**
     * UPDATE manifested order
     */
    const ACTION_UPDATE = 'update';

    /**
     * Create order at the platform and retrieve applicable shipping options.
     *
     * For quoting only (if the order is not yet complete/placed) set additional request parameter `persist=false`.
     *
     * @param OrderRequestInterface $request
     * @return QualifyOrderInterface
     * @throws AdapterException
     */
    public function createOrder(OrderRequestInterface $request);

    /**
     * Manifest order and create open ("pickup requested") pickup fulfillment.
     *
     * For quoting only (if the order is not yet complete/placed), to retrieve
     * pickup locations, set additional request parameter `persist=false`.
     *
     * @param OrderRequestInterface $request
     * @return QualifyOrderInterface
     * @throws AdapterException
     */
    public function createPickupOrder(OrderRequestInterface $request);

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
     * @return QualifyOrderInterface
     * @throws AdapterException
     */
    public function updateOrder(OrderRequestInterface $request);
}
