<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields;

/**
 * Temando API Order Qualification Resource Object Attributes
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderQualificationAttributes
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\OrderQualification\Experience[]
     */
    private $experiences = [];

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\OrderQualification\Experience[]
     */
    public function getExperiences()
    {
        return $this->experiences;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\OrderQualification\Experience[] $experiences
     * @return void
     */
    public function setExperiences(array $experiences)
    {
        $this->experiences = $experiences;
    }
}
