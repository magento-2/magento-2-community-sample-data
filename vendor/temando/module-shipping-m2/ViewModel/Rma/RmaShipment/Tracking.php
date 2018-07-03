<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Rma\RmaShipment;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Rma\Api\Data\RmaInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\ViewModel\RmaAccessInterface;

/**
 * View model for Rma Tracking.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Tracking implements ArgumentInterface, RmaAccessInterface
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
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * RmaView constructor.
     * @param RmaAccess $rmaAccess
     * @param UrlInterface $urlBuilder
     * @param Carrier $carrier
     */
    public function __construct(
        RmaAccess $rmaAccess,
        UrlInterface $urlBuilder,
        Carrier $carrier
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->carrier = $carrier;
        $this->rmaAccess = $rmaAccess;
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
        $shipment =  $this->rmaAccess->getCurrentRmaShipment();
        $trackingNumber = $shipment->getFulfillment()->getTrackingReference();

        return $trackingNumber;
    }

    /**
     * @return string
     */
    public function getCarrierTitle()
    {
        $shipment =  $this->rmaAccess->getCurrentRmaShipment();
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
                'shipment_id' =>  $this->rmaAccess->getCurrentRmaShipment()->getShipmentId(),
                '_secure' => true,
            ]
        );
    }

    /**
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface
    {
        /** @var \Magento\Rma\Model\Rma $rma */
        $rma = $this->getRma();
        return $rma->getOrder();
    }

    /**
     * @return RmaInterface
     */
    public function getRma(): RmaInterface
    {
        return $this->rmaAccess->getCurrentRma();
    }

    /**
     * @deprecated since 1.2.0 | no longer available
     * @return ShipmentInterface
     */
    public function getRmaShipment(): ShipmentInterface
    {
        return $this->rmaAccess->getCurrentRmaShipment();
    }

    /**
     * Check if Return Shipment Exists.
     *
     * @return boolean
     */
    public function hasReturnShipment()
    {
        $returnShipment = $this->rmaAccess->getCurrentRmaShipment();

        return $returnShipment ? true : false;
    }

    /**
     * @return string
     */
    public function getShippingDescription()
    {
        $order = $this->getOrder();
        if (!$order) {
            return '';
        }

        return $order->getShippingDescription();
    }
}
