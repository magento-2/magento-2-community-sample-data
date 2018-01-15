<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Adapter;

use Temando\Shipping\Rest\Request\OrderRequestInterface;
use Temando\Shipping\Rest\Response\UpdateOrder;
use Temando\Shipping\Rest\Exception\AdapterException;

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
     * @param OrderRequestInterface $request
     *
     * @return UpdateOrder
     * @throws AdapterException
     */
    public function createOrder(OrderRequestInterface $request);

    /**
     * @param OrderRequestInterface $request
     *
     * @return UpdateOrder
     * @throws AdapterException
     */
    public function updateOrder(OrderRequestInterface $request);
}
