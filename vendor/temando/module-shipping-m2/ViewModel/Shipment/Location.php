<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Shipment;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\Location\OrderAddressFactory;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment\CapabilityInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\ViewModel\DataProvider\OrderAddress as AddressRenderer;

/**
 * View model for shipment locations.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Location implements ArgumentInterface
{
    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @var OrderAddressFactory
     */
    private $addressFactory;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * Location constructor.
     * @param ShipmentProviderInterface $shipmentProvider
     * @param RmaAccess $rmaAccess
     * @param OrderAddressFactory $addressFactory
     * @param AddressRenderer $addressRenderer
     */
    public function __construct(
        ShipmentProviderInterface $shipmentProvider,
        RmaAccess $rmaAccess,
        OrderAddressFactory $addressFactory,
        AddressRenderer $addressRenderer
    ) {
        $this->shipmentProvider = $shipmentProvider;
        $this->rmaAccess = $rmaAccess;
        $this->addressFactory = $addressFactory;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * Detect whether delivery address is a regular address or some other
     * destination type like collection point, click & collect store, etc.
     *
     * @return bool
     */
    private function isRegularAddress()
    {
        $shipment = $this->shipmentProvider->getShipment();

        // iterate through all capabilities, conditionally switch `isRegular` flag to `false`
        $isRegular = array_reduce($shipment->getCapabilities(), function ($isRegular, CapabilityInterface $capability) {
            $capabilityId = $capability->getCapabilityId();
            $properties = $capability->getProperties();
            $isCapabilityActive = (isset($properties['required']) && $properties['required'] === true);

            if ($capabilityId === 'collectionPoints' && $isCapabilityActive) {
                return false;
            }

            return $isRegular;
        }, true);

        return $isRegular;
    }

    /**
     * @return string
     */
    public function getShipFromAddressHtml()
    {
        /** @var \Temando\Shipping\Model\ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        if (!$shipment) {
            return '';
        }

        $originLocation = $shipment->getOriginLocation();
        $address = $this->addressFactory->createFromShipmentLocation($originLocation);
        return $this->addressRenderer->getFormattedAddress($address);
    }

    /**
     * @return string
     */
    public function getShipToAddressHtml()
    {
        /** @var \Temando\Shipping\Model\ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        if (!$shipment) {
            return '';
        }

        $destinationLocation = $shipment->getDestinationLocation();
        $address = $this->addressFactory->createFromShipmentLocation($destinationLocation);
        return $this->addressRenderer->getFormattedAddress($address);
    }

    /**
     * @return string
     */
    public function getFinalRecipientAddressHtml()
    {
        /** @var \Temando\Shipping\Model\ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        if (!$shipment) {
            // no platform shipment available
            return '';
        }

        $finalRecipientLocation = $shipment->getFinalRecipientLocation();
        if (!$finalRecipientLocation) {
            // final recipient property is not available
            return '';
        }

        if ($this->isRegularAddress()) {
            // destination equals final recipient, no need to display anything
            return '';
        }

        $address = $this->addressFactory->createFromShipmentLocation($finalRecipientLocation);
        return $this->addressRenderer->getFormattedAddress($address);
    }

    /**
     * @return string
     */
    public function getReturnFromAddressHtml()
    {
        $shipment = $this->rmaAccess->getCurrentRmaShipment();
        if (!$shipment) {
            return '';
        }

        $originLocation = $this->rmaAccess->getCurrentRmaShipment()->getOriginLocation();
        $address = $this->addressFactory->createFromShipmentLocation($originLocation);
        return $this->addressRenderer->getFormattedAddress($address);
    }

    /**
     * @return string
     */
    public function getReturnToAddressHtml()
    {
        $shipment = $this->rmaAccess->getCurrentRmaShipment();
        if (!$shipment) {
            return '';
        }

        $destinationLocation = $this->rmaAccess->getCurrentRmaShipment()->getDestinationLocation();
        $address = $this->addressFactory->createFromShipmentLocation($destinationLocation);
        return $this->addressRenderer->getFormattedAddress($address);
    }

    /**
     * Check if shipment has a origin location.
     *
     * @return bool
     */
    public function hasOriginLocation()
    {
        /** @var \Temando\Shipping\Model\ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        if (!$shipment) {
            return false;
        }

        $originLocation = $shipment->getOriginLocation();

        return $originLocation ? true : false;
    }
}
