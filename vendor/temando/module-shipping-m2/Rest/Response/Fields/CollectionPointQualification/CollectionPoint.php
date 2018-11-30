<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\CollectionPointQualification;

/**
 * Temando API Collection Point Qualification Collection Point Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
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
     * @var \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Distance
     */
    private $distance;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\LocationAttributes
     */
    private $location;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Constraints
     */
    private $constraints;

    /**
     * @var string[]
     */
    private $integrationServiceIds;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Capabilities
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
     * @return void
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
     * @return void
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
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Distance
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Distance $distance
     * @return void
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\LocationAttributes
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\LocationAttributes $location
     * @return void
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Constraints
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Constraints $constraints
     * @return void
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
     * @return void
     */
    public function setIntegrationServiceIds($integrationServiceIds)
    {
        $this->integrationServiceIds = $integrationServiceIds;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Capabilities
     */
    public function getCapabilities()
    {
        return $this->capabilities;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Capabilities $capabilities
     * @return void
     */
    public function setCapabilities($capabilities)
    {
        $this->capabilities = $capabilities;
    }
}
