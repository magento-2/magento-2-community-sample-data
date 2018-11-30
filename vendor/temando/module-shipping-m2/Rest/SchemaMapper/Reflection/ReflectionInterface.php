<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper\Reflection;

/**
 * Wrapper for Reflection API access.
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ReflectionInterface
{
    /**
     * Obtain the data type of a class property.
     *
     * @param \stdClass $type
     * @param string $property
     * @return string
     */
    public function getPropertyType($type, $property);

    /**
     * Obtain the return type of a class property getter.
     *
     * @param \stdClass $type
     * @param string $getter
     * @return string
     */
    public function getReturnValueType($type, $getter);
}
