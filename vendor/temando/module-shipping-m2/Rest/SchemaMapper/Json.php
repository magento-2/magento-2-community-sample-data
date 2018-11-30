<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper;

/**
 * Temando REST API JSON Parser
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Json extends AbstractParser implements ParserInterface
{
    /**
     * @param string $data The data to be parsed
     * @param string $type The type (interface) to map the data to
     * @return mixed The object with populated properties
     */
    public function parse($data, $type)
    {
        $properties = json_decode($data, true);
        return $this->parseProperties($properties, $type);
    }
}
