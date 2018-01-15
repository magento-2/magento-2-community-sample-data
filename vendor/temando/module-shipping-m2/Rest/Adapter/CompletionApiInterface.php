<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Adapter;

use Temando\Shipping\Rest\Request\ItemRequestInterface;
use Temando\Shipping\Rest\Request\ListRequestInterface;
use Temando\Shipping\Rest\Response\Type\CompletionResponseType;
use Temando\Shipping\Rest\Exception\AdapterException;

/**
 * The Temando Completion API interface defines the supported subset of operations
 * as available at the Temando API.
 *
 * @package  Temando\Shipping\Rest
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CompletionApiInterface
{
    /**
     * @param ItemRequestInterface $request
     *
     * @return CompletionResponseType
     * @throws AdapterException
     */
    public function getCompletion(ItemRequestInterface $request);

    /**
     * @param ListRequestInterface $request
     *
     * @return CompletionResponseType[]
     * @throws AdapterException
     */
    public function getCompletions(ListRequestInterface $request);
}
