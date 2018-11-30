<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Remove order items listing from New Shipment page.
 *
 * @package  Temando\Shipping\Observer
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class RemoveOrderItemsObserver implements ObserverInterface
{
    /**
     * Temando uses its own items listing component. Remove the original order items listing.
     * - event: layout_generate_blocks_after
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');
        if ($action !== 'adminhtml_order_shipment_new') {
            return;
        }

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getData('layout');
        $formBlock = $layout->getBlock('form');
        if (!$formBlock instanceof \Magento\Shipping\Block\Adminhtml\Create\Form) {
            return;
        }

        $order = $formBlock->getOrder();
        if (!$order instanceof OrderInterface || !$order->getData('shipping_method')) {
            // wrong type, virtual or corrupt order
            return;
        }

        $shippingMethod = $order->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') !== Carrier::CODE) {
            return;
        }

        $formBlock->unsetChild('order_items');
    }
}
