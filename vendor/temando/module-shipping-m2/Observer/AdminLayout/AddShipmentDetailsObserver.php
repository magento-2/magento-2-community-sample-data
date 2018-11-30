<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer\AdminLayout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;

/**
 * Change templates for Temando shipments
 *
 * @package Temando\Shipping\Observer
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class AddShipmentDetailsObserver implements ObserverInterface
{
    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * AddShipmentDetailsObserver constructor.
     * @param ShipmentProviderInterface $shipmentProvider
     */
    public function __construct(ShipmentProviderInterface $shipmentProvider)
    {
        $this->shipmentProvider = $shipmentProvider;
    }

    /**
     * Temando provides additional shipment details compared to the default carriers:
     * - external order reference
     * - external shipment reference
     * - origin location
     * - delivery location
     * - final recipient location
     * - add-ons
     * - documentation
     * - export details
     * Apply a custom template that includes child blocks with these data items.
     *
     * - event: layout_load_before
     *
     * Templates will only be changed if a shipment was loaded from the API.
     * @see \Temando\Shipping\Plugin\Shipping\Order\ShipmentLoaderPlugin::afterLoad
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $applicableActions = [
            'adminhtml_order_shipment_view',
        ];

        $action = $observer->getData('full_action_name');
        if (!in_array($action, $applicableActions)) {
            // not the shipment details page
            return;
        }

        $shipment = $this->shipmentProvider->getShipment();
        if (!$shipment) {
            // no additional data to display
            return;
        }

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getData('layout');
        $layout->getUpdate()->addHandle('temando_order_shipment_view');

        $originCountry = $shipment->getOriginLocation()->getCountryCode();
        $destinationCountry = $shipment->getDestinationLocation()->getCountryCode();
        if ($originCountry !== $destinationCountry) {
            // add additional item data for cross border shipments
            $layout->getUpdate()->addHandle('temando_order_shipment_view_xb');
        }
    }
}
