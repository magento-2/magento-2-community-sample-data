<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper\Reflection;

/**
 * Temando Property Handler
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface PropertyHandlerInterface
{
    /**
     * Convert snake case to UpperCamelCase.
     *
     * @param string $key
     * @return string
     */
    public function camelizeUp($key);

    /**
     * Convert snake case to lowerCamelCase.
     *
     * @param string $key
     * @return string
     */
    public function camelizeLow($key);

    /**
     * Convert Capitalized, UpperCamelCase or lowerCamelCase to snake case.
     *
     * @param string $key
     * @return string
     */
    public function underscore($key);

    /**
     * @param string $key
     * @return string
     */
    public function getter($key);

    /**
     * @param string $key
     * @return string
     */
    public function setter($key);
}
