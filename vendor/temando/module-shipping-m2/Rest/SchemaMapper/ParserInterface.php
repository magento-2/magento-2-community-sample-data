<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper;

/**
 * Temando Rest Data Parser
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ParserInterface
{
    /**
     * Convert the string representation of a given type. The input data format
     * (xml, json, etc.) is handled by the concrete parser class.
     *
     * @param string $data The data to be parsed
     * @param string $type The type (interface) to map the data to
     * @return mixed The object with populated properties
     */
    public function parse($data, $type);
}
