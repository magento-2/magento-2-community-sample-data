<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Request;

/**
 * StreamCreateRequestInterface
 *
 * @package  Temando\Shipping\Rest
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface StreamCreateRequestInterface
{
    /**
     * @return string
     */
    public function getRequestBody();
}
