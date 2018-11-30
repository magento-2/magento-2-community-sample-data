<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Shipment;

use Temando\Shipping\Rest\Response\Fields\Shipment\Fulfill\CarrierBooking;

/**
 * Temando API Shipment Fulfill Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Fulfill
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Shipment\Fulfill\CarrierBooking
     */
    private $carrierBooking;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Shipment\Fulfill\CarrierBooking
     */
    public function getCarrierBooking()
    {
        return $this->carrierBooking;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Shipment\Fulfill\CarrierBooking $carrierBooking
     * @return void
     */
    public function setCarrierBooking(CarrierBooking $carrierBooking)
    {
        $this->carrierBooking = $carrierBooking;
    }
}
