<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Generic;

/**
 * Temando API Weight Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Weight
{
    /**
     * @var float
     */
    private $value;

    /**
     * @var string
     */
    private $unitOfMeasurement;

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unitOfMeasurement;
    }

    /**
     * @param string $unitOfMeasurement
     * @return void
     */
    public function setUnit($unitOfMeasurement)
    {
        $this->unitOfMeasurement = $unitOfMeasurement;
    }

    /**
     * @return string
     */
    public function getUnitOfMeasurement()
    {
        return $this->unitOfMeasurement;
    }

    /**
     * @param string $unitOfMeasurement
     * @return void
     */
    public function setUnitOfMeasurement($unitOfMeasurement)
    {
        $this->unitOfMeasurement = $unitOfMeasurement;
    }
}
