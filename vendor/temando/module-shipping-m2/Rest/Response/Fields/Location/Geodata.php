<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Location;

use Temando\Shipping\Rest\Response\Fields\Location\Geodata\Zone;

/**
 * Temando API Location Geo Data Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Geodata
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Location\Geodata\Zone
     */
    private $zone;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Location\Geodata\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Location\Geodata\Zone
     * @return void
     */
    public function setZone(Zone $zone)
    {
        $this->zone = $zone;
    }
}
