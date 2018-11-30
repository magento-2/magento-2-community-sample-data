<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\DataObject;

use Temando\Shipping\Rest\Response\Fields\BatchAttributes;

/**
 * Temando API Batch Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Batch extends AbstractResource
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\BatchAttributes
     */
    private $attributes;

    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Shipment[]
     */
    private $shipments = [];

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\BatchAttributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\BatchAttributes $attributes
     * @return void
     */
    public function setAttributes(BatchAttributes $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Shipment[]
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Shipment[] $shipments
     * @return void
     */
    public function setShipments(array $shipments)
    {
        $this->shipments = $shipments;
    }
}
