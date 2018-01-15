<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\CarrierIntegration;

/**
 * Temando API Carrier Integration Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Attributes
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\CarrierIntegration\Attributes\Service[]
     */
    private $services = [];

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
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     * @return void
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\CarrierIntegration\Attributes\Service[]
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\CarrierIntegration\Attributes\Service[] $services
     * @return void
     */
    public function setServices(array $services)
    {
        $this->services = $services;
    }
}
