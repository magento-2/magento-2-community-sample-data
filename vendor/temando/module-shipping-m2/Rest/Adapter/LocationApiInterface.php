<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Adapter;

use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterface;
use Temando\Shipping\Rest\Request\ListRequestInterface;
use Temando\Shipping\Rest\Response\DataObject\Location;

/**
 * The Temando Location API interface defines the supported subset of operations
 * as available at the Temando API.
 *
 * @package  Temando\Shipping\Rest
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface LocationApiInterface
{
    /**
     * @param ListRequestInterface $request
     *
     * @return Location[]
     * @throws AdapterException
     */
    public function getLocations(ListRequestInterface $request);

    /**
     * @param ItemRequestInterface $request
     *
     * @return void
     * @throws AdapterException
     */
    public function deleteLocation(ItemRequestInterface $request);
}
