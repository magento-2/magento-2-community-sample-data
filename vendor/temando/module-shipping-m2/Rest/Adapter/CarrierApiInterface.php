<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Adapter;

use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterface;
use Temando\Shipping\Rest\Request\ListRequestInterface;
use Temando\Shipping\Rest\Response\DataObject\CarrierConfiguration;
use Temando\Shipping\Rest\Response\DataObject\CarrierIntegration;

/**
 * The Temando Carriers API interface defines the supported subset of operations
 * as available at the Temando API.
 *
 * @package  Temando\Shipping\Rest
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CarrierApiInterface
{
    /**
     * @param ListRequestInterface $request
     *
     * @return CarrierConfiguration[]
     * @throws AdapterException
     */
    public function getCarrierConfigurations(ListRequestInterface $request);

    /**
     * @param ListRequestInterface $request
     *
     * @return CarrierIntegration[]
     * @throws AdapterException
     */
    public function getCarrierIntegrations(ListRequestInterface $request);

    /**
     * @param ItemRequestInterface $request
     *
     * @return void
     * @throws AdapterException
     */
    public function deleteCarrierConfiguration(ItemRequestInterface $request);
}
