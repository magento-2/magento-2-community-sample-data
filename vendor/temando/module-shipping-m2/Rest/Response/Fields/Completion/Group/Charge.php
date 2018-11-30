<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Completion\Group;

use Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue;

/**
 * Temando API Completion Group Charge Field
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Charge
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    private $amount;

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue $amount
     * @return void
     */
    public function setAmount(MonetaryValue $amount)
    {
        $this->amount = $amount;
    }
}
