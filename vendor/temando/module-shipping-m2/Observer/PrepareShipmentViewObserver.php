<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\ViewModel\Order\OrderDetails;
use Temando\Shipping\ViewModel\Shipment\Location;
use Temando\Shipping\ViewModel\Shipment\ShipmentDetails;

/**
 * Change shipment view template for temando shipments.
 *
 * @package  Temando\Shipping\Observer
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class PrepareShipmentViewObserver implements ObserverInterface
{
    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * @var Location
     */
    private $locationViewModel;

    /**
     * @var OrderDetails
     */
    private $orderDetailsViewModel;

    /**
     * @var ShipmentDetails
     */
    private $shipmentDetailsViewModel;

    /**
     * PrepareShipmentViewObserver constructor.
     * @param ShipmentProviderInterface $shipmentProvider
     * @param Location $locationViewModel
     * @param OrderDetails $orderDetailsViewModel
     * @param ShipmentDetails $shipmentDetailsViewModel
     */
    public function __construct(
        ShipmentProviderInterface $shipmentProvider,
        Location $locationViewModel,
        OrderDetails $orderDetailsViewModel,
        ShipmentDetails $shipmentDetailsViewModel
    ) {
        $this->shipmentProvider = $shipmentProvider;
        $this->locationViewModel = $locationViewModel;
        $this->orderDetailsViewModel = $orderDetailsViewModel;
        $this->shipmentDetailsViewModel = $shipmentDetailsViewModel;
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
     * - event: layout_generate_blocks_after
     *
     * Templates will only be changed if a shipment was loaded from the API.
     * @see \Temando\Shipping\Plugin\Shipping\Order\ShipmentLoaderPlugin::afterLoad
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');
        if ($action !== 'adminhtml_order_shipment_view') {
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

        $formBlock = $layout->getBlock('form');
        if ($formBlock instanceof \Magento\Shipping\Block\Adminhtml\View\Form) {
            // set template to render documentation, export details, add-ons
            $formBlock->setTemplate('Temando_Shipping::shipping/view/form.phtml');
        }

        $infoBlock = $layout->getBlock('order_info');
        if ($infoBlock instanceof \Magento\Sales\Block\Adminhtml\Order\View\Info) {
            // set template to render external entity references and addresses from platform
            $infoBlock->setTemplate('Temando_Shipping::sales/order/shipment/view/info.phtml');
            $infoBlock->setData('locationViewModel', $this->locationViewModel);
            $infoBlock->setData('orderDetailsViewModel', $this->orderDetailsViewModel);
            $infoBlock->setData('shipmentDetailsViewModel', $this->shipmentDetailsViewModel);
        }

        $originCountry = $shipment->getOriginLocation()->getCountryCode();
        $destinationCountry = $shipment->getDestinationLocation()->getCountryCode();
        if ($originCountry === $destinationCountry) {
            // no additional item data to display for national shipments
            return;
        }

        $itemsBlock = $layout->getBlock('shipment_items');
        if ($itemsBlock instanceof \Magento\Shipping\Block\Adminhtml\View\Items) {
            // set template to render international package details
            $itemsBlock->setTemplate('Temando_Shipping::shipment/package_items.phtml');
            $itemsBlock->setData('viewModel', $this->shipmentDetailsViewModel);
        }
    }
}
