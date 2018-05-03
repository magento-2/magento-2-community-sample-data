<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill;

/**
 * Temando API Shipment CarrierBooking Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierBooking
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill\CarrierBooking\ShippingTaxInclusiveCharge
     */
    private $shippingTaxInclusiveCharge;

    /**
     * @var string
     */
    private $trackingReference;

    /**
     * @var string
     */
    private $bookingReference;

    /**
     * @var string
     */
    private $carrierName;

    /**
     * @var string
     */
    private $integrationId;

    /**
     * @var string
     */
    private $integrationServiceId;

    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var string
     */
    private $configurationId;

    /**
     * @return string
     */
    public function getConfigurationId()
    {
        return $this->configurationId;
    }

    /**
     * @param string $configurationId
     */
    public function setConfigurationId($configurationId)
    {
        $this->configurationId = $configurationId;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill\CarrierBooking\ShippingTaxInclusiveCharge
     */
    public function getShippingTaxInclusiveCharge()
    {
        return $this->shippingTaxInclusiveCharge;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill\CarrierBooking\ShippingTaxInclusiveCharge $shippingTaxInclusiveCharge
     * @return void
     */
    public function setShippingTaxInclusiveCharge($shippingTaxInclusiveCharge)
    {
        $this->shippingTaxInclusiveCharge = $shippingTaxInclusiveCharge;
    }

    /**
     * @return string
     */
    public function getTrackingReference()
    {
        return $this->trackingReference;
    }

    /**
     * @param string $trackingReference
     * @return void
     */
    public function setTrackingReference($trackingReference)
    {
        $this->trackingReference = $trackingReference;
    }

    /**
     * @return string
     */
    public function getBookingReference()
    {
        return $this->bookingReference;
    }

    /**
     * @param string $bookingReference
     * @return void
     */
    public function setBookingReference($bookingReference)
    {
        $this->bookingReference = $bookingReference;
    }

    /**
     * @return string
     */
    public function getCarrierName()
    {
        return $this->carrierName;
    }

    /**
     * @param string $carrierName
     * @return void
     */
    public function setCarrierName($carrierName)
    {
        $this->carrierName = $carrierName;
    }

    /**
     * @return string
     */
    public function getIntegrationId()
    {
        return $this->integrationId;
    }

    /**
     * @param string $integrationId
     * @return void
     */
    public function setIntegrationId($integrationId)
    {
        $this->integrationId = $integrationId;
    }

    /**
     * @return string
     */
    public function getIntegrationServiceId()
    {
        return $this->integrationServiceId;
    }

    /**
     * @param string $integrationServiceId
     * @return void
     */
    public function setIntegrationServiceId($integrationServiceId)
    {
        $this->integrationServiceId = $integrationServiceId;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     * @return void
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }
}
