<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Shipment;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment\CapabilityInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;

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
     * @var OrderAddressInterfaceFactory
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
     * @param OrderAddressInterfaceFactory $addressFactory
     * @param AddressRenderer $addressRenderer
     */
    public function __construct(
        ShipmentProviderInterface $shipmentProvider,
        RmaAccess $rmaAccess,
        OrderAddressInterfaceFactory $addressFactory,
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
     * @param string[] $addressData
     * @return string
     */
    private function getFormattedAddress(array $addressData)
    {
        /** @var \Magento\Sales\Model\Order\Address $address */
        $address = $this->addressFactory->create(['data' => $addressData]);
        $formattedAddress = $this->addressRenderer->format($address, 'html');
        return (string) $formattedAddress;
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
        $addressData = [
            'region'     => $originLocation->getRegionCode(),
            'postcode'   => $originLocation->getPostalCode(),
            'lastname'   => $originLocation->getPersonLastName(),
            'street'     => $originLocation->getStreet(),
            'city'       => $originLocation->getCity(),
            'email'      => $originLocation->getEmail(),
            'telephone'  => $originLocation->getPhoneNumber(),
            'country_id' => $originLocation->getCountryCode(),
            'firstname'  => $originLocation->getPersonFirstName(),
            'company'    => $originLocation->getCompany()
        ];

        return $this->getFormattedAddress($addressData);
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
        $addressData = [
            'region'     => $destinationLocation->getRegionCode(),
            'postcode'   => $destinationLocation->getPostalCode(),
            'lastname'   => $destinationLocation->getPersonLastName(),
            'street'     => $destinationLocation->getStreet(),
            'city'       => $destinationLocation->getCity(),
            'email'      => $destinationLocation->getEmail(),
            'telephone'  => $destinationLocation->getPhoneNumber(),
            'country_id' => $destinationLocation->getCountryCode(),
            'firstname'  => $destinationLocation->getPersonFirstName(),
            'company'    => $destinationLocation->getCompany()
        ];

        return $this->getFormattedAddress($addressData);
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

        $addressData = [
            'region'     => $finalRecipientLocation->getRegionCode(),
            'postcode'   => $finalRecipientLocation->getPostalCode(),
            'lastname'   => $finalRecipientLocation->getPersonLastName(),
            'street'     => $finalRecipientLocation->getStreet(),
            'city'       => $finalRecipientLocation->getCity(),
            'email'      => $finalRecipientLocation->getEmail(),
            'telephone'  => $finalRecipientLocation->getPhoneNumber(),
            'country_id' => $finalRecipientLocation->getCountryCode(),
            'firstname'  => $finalRecipientLocation->getPersonFirstName(),
            'company'    => $finalRecipientLocation->getCompany()
        ];

        return $this->getFormattedAddress($addressData);
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
        $addressData = [
            'firstname'  => $originLocation->getPersonFirstName(),
            'lastname'   => $originLocation->getPersonLastName(),
            'company'    => $originLocation->getCompany(),
            'street'     => $originLocation->getStreet(),
            'region'     => $originLocation->getRegionCode(),
            'city'       => $originLocation->getCity(),
            'postcode'   => $originLocation->getPostalCode(),
            'country_id' => $originLocation->getCountryCode(),
            'email'      => $originLocation->getEmail(),
            'telephone'  => $originLocation->getPhoneNumber()
        ];

        return $this->getFormattedAddress($addressData);
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
        $addressData = [
            'firstname'  => $destinationLocation->getPersonFirstName(),
            'lastname'   => $destinationLocation->getPersonLastName(),
            'company'    => $destinationLocation->getCompany(),
            'street'     => $destinationLocation->getStreet(),
            'region'     => $destinationLocation->getRegionCode(),
            'city'       => $destinationLocation->getCity(),
            'postcode'   => $destinationLocation->getPostalCode(),
            'country_id' => $destinationLocation->getCountryCode(),
            'email'      => $destinationLocation->getEmail(),
            'telephone'  => $destinationLocation->getPhoneNumber()
        ];

        return $this->getFormattedAddress($addressData);
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
