<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Temando\Shipping\Model\Shipment\PackageCollection;
use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Model\Shipping\Carrier;

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
     * PrepareShipmentViewObserver constructor.
     * @param ShipmentProviderInterface $shipmentProvider
     */
    public function __construct(ShipmentProviderInterface $shipmentProvider)
    {
        $this->shipmentProvider = $shipmentProvider;
    }

    /**
     * Temando provides additional shipment details compared to the default
     * carriers: origin location and documentation. Apply a custom template that
     * includes child blocks with these data items.
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
            $formBlock->setTemplate('Temando_Shipping::shipping/view/form.phtml');
        }

        $originCountry = $shipment->getOriginLocation()->getCountryCode();
        $destinationCountry = $shipment->getDestinationLocation()->getCountryCode();
        if ($originCountry === $destinationCountry) {
            // no additional item data to display for national shipments
            return;
        }

        $itemsBlock = $layout->getBlock('shipment_items');
        if ($itemsBlock instanceof \Magento\Shipping\Block\Adminhtml\View\Items) {
            $itemsBlock->setTemplate('Temando_Shipping::shipping/view/form/items.phtml');

            $packages = $shipment->getPackages();
            if ($packages instanceof PackageCollection) {
                $packages = $packages->getArrayCopy();
            }

            // if one day packages should be displayed separately, set package collection to the block
            $items = array_reduce($packages, function (array $items, PackageInterface $package) {
                $items = array_merge($items, $package->getItems());
                return $items;
            }, []);

            $itemsBlock->setData('items', $items);
        }
    }
}
