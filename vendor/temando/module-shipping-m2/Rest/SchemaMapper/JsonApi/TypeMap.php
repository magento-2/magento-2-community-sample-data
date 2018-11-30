<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper\JsonApi;

/**
 * Temando REST API JSON API Type Map Interface
 *
 * Provide JSON API type to PHP type mappings. Useful for mixed-type collections
 * that cannot be annotated.
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class TypeMap implements TypeMapInterface
{
    /**
     * @var string[]
     */
    private $classes = [];

    /**
     * TypeMap constructor.
     * @param string[] $classes
     */
    public function __construct(array $classes = [])
    {
        $this->classes = $classes;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getClass($type)
    {
        if (!isset($this->classes[$type])) {
            return '';
        }

        return $this->classes[$type];
    }
}
