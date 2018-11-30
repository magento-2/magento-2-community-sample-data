<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Get Location Collection Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetLocations implements GetLocationsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Location[]
     */
    private $data = [];

    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Location[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Location[] $locations
     * @return void
     */
    public function setData(array $locations)
    {
        $this->data = $locations;
    }
}
