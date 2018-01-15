<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type\Generic;

use Temando\Shipping\Rest\Request\Type\EmptyFilterableInterface;
use Temando\Shipping\Rest\Request\Type\AttributeFilter;

/**
 * Temando API Weight
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Weight implements \JsonSerializable, EmptyFilterableInterface
{
    /**
     * @var float
     */
    private $value;

    /**
     * @var string
     */
    private $unit;

    /**
     * @param string $value
     * @return float
     */
    private function getValue($value)
    {
        return floatval($value);
    }

    /**
     * @param string $unitIn
     * @return string
     */
    private function getUnit($unitIn)
    {
        $map = [
            'g' => 'g',
            'gram' => 'gram',
            'oz' => 'oz',
            'ounce' => 'ounce',
            'kg' => 'kg',
            'kilogram' => 'kilogram',
            'kgs' => 'kg',
            'lb' => 'lb',
            'pound' => 'pound',
            'lbs' => 'lb',
        ];
        $unitOut = isset($map[$unitIn]) ? $map[$unitIn] : $unitIn;

        return $unitOut;
    }

    /**
     * Dimensions constructor.
     * @param float $value
     * @param string $unitOfMeasurement
     */
    public function __construct($value, $unitOfMeasurement)
    {
        $this->value = $value;
        $this->unit = $unitOfMeasurement;
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

    /**
     * @return mixed[]
     */
    public function jsonSerialize()
    {
        return [
            'value' => $this->getValue($this->value),
            'unit' => $this->getUnit($this->unit),
        ];
    }
}
