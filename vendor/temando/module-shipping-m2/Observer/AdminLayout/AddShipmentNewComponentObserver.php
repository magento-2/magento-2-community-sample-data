<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer\AdminLayout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Add OrderShip component
 *
 * @package Temando\Shipping\Observer
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class AddShipmentNewComponentObserver implements ObserverInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * AddPickupTabObserver constructor.
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Temando replaces the default packaging popup. Add the OrderShip component
     * to the "content" container of the New Shipment page as well as additional
     * order details.
     *
     * - event: layout_load_before
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $applicableActions = [
            'adminhtml_order_shipment_new',
        ];

        $action = $observer->getData('full_action_name');
        if (!in_array($action, $applicableActions)) {
            // not the new shipment page
            return;
        }

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->registry->registry('current_shipment');
        if (!$shipment instanceof ShipmentInterface) {
            // no additional data to display
            return;
        }

        $order = $shipment->getOrder();
        if (!$order instanceof OrderInterface) {
            return;
        }

        if ($order->getIsVirtual() || !$order->getData('shipping_method')) {
            return;
        }

        $shippingMethod = $order->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') !== Carrier::CODE) {
            return;
        }

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getData('layout');
        // display additional order details
        $layout->getUpdate()->addHandle('temando_order_info');
        // add OrderShip component
        $layout->getUpdate()->addHandle('temando_order_ship_component');
    }
}
