<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Get Locations Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class GetLocations implements GetLocationsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\LocationResponseType[]
     */
    private $data = [];

    /**
     * @return \Temando\Shipping\Rest\Response\Type\LocationResponseType[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param  \Temando\Shipping\Rest\Response\Type\LocationResponseType[] $locations
     * @return void
     */
    public function setData(array $locations)
    {
        $this->data = $locations;
    }
}
