<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Shipping\View;

use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;

/**
 * Temando Origin Location Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 * @deprecated since 1.0.5 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Order\Location
 */
class OriginLocation extends BackendTemplate
{
    /**
     * @var OrderAddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * OriginLocation constructor.
     *
     * @param Context                      $context
     * @param OrderAddressInterfaceFactory $addressFactory
     * @param AddressRenderer              $addressRenderer
     * @param ShipmentProviderInterface    $shipmentProvider
     * @param mixed[]                      $data
     */
    public function __construct(
        Context $context,
        OrderAddressInterfaceFactory $addressFactory,
        AddressRenderer $addressRenderer,
        ShipmentProviderInterface $shipmentProvider,
        array $data = []
    ) {
        $this->addressFactory   = $addressFactory;
        $this->addressRenderer  = $addressRenderer;
        $this->shipmentProvider = $shipmentProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getFormattedAddress()
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
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Sales\Model\Order\Address $address */
        $address = $this->addressFactory->create(['data' => $addressData]);
        $formattedAddress = $this->addressRenderer->format($address, 'html');

        return (string) $formattedAddress;
    }
}
