<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Order\Included\Attributes;

/**
 * Temando API Order Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Experience
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Order\Included\Attributes\Experience\Cost[]
     */
    private $cost = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Order\Included\Attributes\Experience\Description[]
     */
    private $description = [];

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
     * @return \Temando\Shipping\Rest\Response\Type\Order\Included\Attributes\Experience\Cost[]
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Order\Included\Attributes\Experience\Cost[] $cost
     * @return void
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Order\Included\Attributes\Experience\Description[]
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Order\Included\Attributes\Experience\Description[] $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
