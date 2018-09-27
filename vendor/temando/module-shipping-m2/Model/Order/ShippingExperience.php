<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;

/**
 * Shipping Experience as selected during checkout, used during REST API access
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShippingExperience implements ShippingExperienceInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $code;

    /**
     * @var float
     */
    private $cost;

    /**
     * ShippingExperience constructor.
     * @param string $label
     * @param string $code
     * @param float $cost
     */
    public function __construct($label, $code, $cost)
    {
        $this->label = $label;
        $this->code = $code;
        $this->cost = $cost;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return void
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return void
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param float $cost
     * @return void
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * Obtain serialized representation of a shipping experience.
     *
     * @return string[]
     */
    public function jsonSerialize()
    {
        $properties = get_object_vars($this);
        return $properties;
    }
}
