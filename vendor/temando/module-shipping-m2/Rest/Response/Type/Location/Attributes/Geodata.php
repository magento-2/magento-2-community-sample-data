<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Location\Attributes;

use Temando\Shipping\Rest\Response\Type\Location\Attributes\Geodata\Zone;

/**
 * Temando API Location Geo Data Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Geodata
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Location\Attributes\Geodata\Zone
     */
    private $zone;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Location\Attributes\Geodata\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Location\Attributes\Geodata\Zone $zone
     * @return void
     */
    public function setZone(Zone $zone)
    {
        $this->zone = $zone;
    }
}
