<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\DataObject;

use Temando\Shipping\Rest\Response\Fields\ShipmentAttributes;

/**
 * Temando API Location Resource Object
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Shipment extends AbstractResource
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\ShipmentAttributes
     */
    private $attributes;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\LocationAttributes[]
     */
    private $locations;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\ShipmentAttributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\ShipmentAttributes $attributes
     * @return void
     */
    public function setAttributes(ShipmentAttributes $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\LocationAttributes[]
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\LocationAttributes[] $locations
     * @return void
     */
    public function setLocations(array $locations)
    {
        $this->locations = $locations;
    }
}
