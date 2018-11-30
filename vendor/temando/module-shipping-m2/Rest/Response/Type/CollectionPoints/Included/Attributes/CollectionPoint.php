<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes;

/**
 * Temando API Order Attributes CollectionPoint Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CollectionPoint
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Distance
     */
    private $distance;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location
     */
    private $location;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Constraints
     */
    private $constraints;

    /**
     * @var string[]
     */
    private $integrationServiceIds;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Capabilities
     */
    private $capabilities;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Distance
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Distance $distance
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Constraints
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Constraints $constraints
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * @return string[]
     */
    public function getIntegrationServiceIds()
    {
        return $this->integrationServiceIds;
    }

    /**
     * @param string[] $integrationServiceIds
     */
    public function setIntegrationServiceIds($integrationServiceIds)
    {
        $this->integrationServiceIds = $integrationServiceIds;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Capabilities
     */
    public function getCapabilities()
    {
        return $this->capabilities;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Capabilities $capabilities
     */
    public function setCapabilities($capabilities)
    {
        $this->capabilities = $capabilities;
    }
}
