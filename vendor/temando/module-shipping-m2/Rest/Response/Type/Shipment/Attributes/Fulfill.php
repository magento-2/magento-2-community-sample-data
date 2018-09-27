<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Shipment\Attributes;

use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill\CarrierBooking;

/**
 * Temando API Shipment Fulfill Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Fulfill
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill\CarrierBooking
     */
    private $carrierBooking;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill\CarrierBooking
     */
    public function getCarrierBooking()
    {
        return $this->carrierBooking;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill\CarrierBooking $carrierBooking
     * @return void
     */
    public function setCarrierBooking(CarrierBooking $carrierBooking)
    {
        $this->carrierBooking = $carrierBooking;
    }
}
