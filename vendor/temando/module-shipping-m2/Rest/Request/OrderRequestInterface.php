<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request;

/**
 * Temando API Order Operation Parameters
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface OrderRequestInterface
{
    /**
     * Obtain url path parameters, i.e. entity ids, in order of appearance.
     *
     * @return string[]
     */
    public function getPathParams();

    /**
     * Obtain query/post parameters.
     *
     * @param string $actionType
     * @return string[]
     */
    public function getRequestParams($actionType);

    /**
     * Obtain raw post data.
     *
     * @return mixed
     */
    public function getRequestBody();
}
