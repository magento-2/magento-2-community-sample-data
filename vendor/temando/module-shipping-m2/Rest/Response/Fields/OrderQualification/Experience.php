<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\OrderQualification;

/**
 * Temando API Order Qualification Experience Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Experience
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\OrderQualification\Experience\Cost[]
     */
    private $cost = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\OrderQualification\Experience\Description[]
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
     * @return \Temando\Shipping\Rest\Response\Fields\OrderQualification\Experience\Cost[]
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\OrderQualification\Experience\Cost[] $cost
     * @return void
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\OrderQualification\Experience\Description[]
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Experience\Description[] $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
