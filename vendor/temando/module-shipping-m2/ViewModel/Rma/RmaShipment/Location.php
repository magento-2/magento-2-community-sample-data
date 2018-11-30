<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Rma\RmaShipment;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;

/**
 * View model for RMA shipment location.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @deprecated since 1.1.3
 * @see \Temando\Shipping\ViewModel\Order\Location
 */
class Location implements ArgumentInterface
{
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
     * @param RmaAccess $rmaAccess
     * @param OrderAddressInterfaceFactory $addressFactory
     * @param AddressRenderer $addressRenderer
     */
    public function __construct(
        RmaAccess $rmaAccess,
        OrderAddressInterfaceFactory $addressFactory,
        AddressRenderer $addressRenderer
    ) {
        $this->rmaAccess = $rmaAccess;
        $this->addressFactory = $addressFactory;
        $this->addressRenderer = $addressRenderer;
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
    public function getReturnFromAddressHtml()
    {
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
}
