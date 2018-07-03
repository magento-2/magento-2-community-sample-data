<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type\Generic;

use Temando\Shipping\Rest\Request\Type\EmptyFilterableInterface;
use Temando\Shipping\Rest\Request\Type\AttributeFilter;

/**
 * Temando API Dimensions
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Dimensions implements \JsonSerializable, EmptyFilterableInterface
{
    /**
     * @var float
     */
    private $length;

    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $height;

    /**
     * @var string
     */
    private $unit;

    /**
     * Dimensions constructor.
     * @param float $length
     * @param float $width
     * @param float $height
     * @param string $unit
     */
    public function __construct($length, $width, $height, $unit)
    {
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->unit = $unit;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize()
    {
        return [
            'length' => floatval($this->length),
            'width' => floatval($this->width),
            'height' => floatval($this->height),
            'unit' => $this->unit,
        ];
    }

    /**
     * Check if any properties are set.
     *
     * @return bool
     */
    public function isEmpty()
    {
        $properties = get_object_vars($this);
        $properties = AttributeFilter::notEmpty($properties);
        return empty($properties);
    }
}
