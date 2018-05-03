<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Rma\RmaShipment;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\ViewModel\Rma\RmaView;
use Temando\Shipping\ViewModel\RmaAccessInterface;

/**
 * View model for Rma Tracking.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Tracking extends RmaView implements ArgumentInterface, RmaAccessInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Carrier
     */
    private $carrier;

    /**
     * RmaView constructor.
     * @param RmaAccess $rmaAccess
     * @param OrderAddressInterfaceFactory $addressFactory
     * @param AddressRenderer $addressRenderer
     * @param UrlInterface $urlBuilder
     * @param Carrier $carrier
     */
    public function __construct(
        RmaAccess $rmaAccess,
        OrderAddressInterfaceFactory $addressFactory,
        AddressRenderer $addressRenderer,
        UrlInterface $urlBuilder,
        Carrier $carrier
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->carrier = $carrier;

        parent::__construct($rmaAccess, $addressFactory, $addressRenderer);
    }

    /**
     * @return string
     */
    public function getCarrierName()
    {
        return $this->carrier->getConfigData('title');
    }

    /**
     * @return string
     */
    public function getTrackingNumber()
    {
        /** @var Shipment $shipment */
        $shipment = $this->getRmaShipment();
        $trackingNumber = $shipment->getFulfillment()->getTrackingReference();

        return $trackingNumber;
    }

    /**
     * @return string
     */
    public function getCarrierTitle()
    {
        $shipment = $this->getRmaShipment();
        $carrierTitle = sprintf(
            '%s - %s',
            $shipment->getFulfillment()->getCarrierName(),
            $shipment->getFulfillment()->getServiceName()
        );

        return $carrierTitle;
    }

    /**
     * Get tracking popup URL.
     */
    public function getTrackingPopUrl()
    {
        return $this->urlBuilder->getUrl(
            'temando/rma_shipment/track',
            [
                'shipment_id' => $this->getRmaShipment()->getShipmentId(),
                '_secure' => true,
            ]
        );
    }
}
