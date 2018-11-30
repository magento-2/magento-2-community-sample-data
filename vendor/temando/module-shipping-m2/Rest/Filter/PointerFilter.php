<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Filter;

/**
 * JSON API Pointer Filter
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.temando.com/
 */
class PointerFilter implements \JsonSerializable
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $value;

    /**
     * PointerFilter constructor.
     * @param string $path
     * @param string $operator
     * @param string $value
     */
    public function __construct($path, $operator, $value)
    {
        $this->path = $path;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
