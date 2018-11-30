<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\CollectionPoints\Included;

/**
 * Temando API Order Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Attributes
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint
     */
    private $collectionPoint;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Experience[]
     */
    private $experiences = [];

    /**
     * @return \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint
     */
    public function getCollectionPoint()
    {
        return $this->collectionPoint;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint $collectionPoint
     */
    public function setCollectionPoint($collectionPoint)
    {
        $this->collectionPoint = $collectionPoint;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Experience[]
     */
    public function getExperiences()
    {
        return $this->experiences;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Experience[] $experiences
     */
    public function setExperiences($experiences)
    {
        $this->experiences = $experiences;
    }
}
