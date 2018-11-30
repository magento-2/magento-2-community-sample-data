<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

use Temando\Shipping\Rest\Response\DataObject\Shipment;

/**
 * Temando API Get Shipment Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetShipment implements GetShipmentInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Shipment
     */
    private $data;

    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Shipment
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Shipment $shipment
     * @return void
     */
    public function setData(Shipment $shipment)
    {
        $this->data = $shipment;
    }
}
