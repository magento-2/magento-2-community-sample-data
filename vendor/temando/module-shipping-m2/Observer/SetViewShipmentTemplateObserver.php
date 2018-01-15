<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Change shipment view template for temando shipments.
 *
 * @package  Temando\Shipping\Observer
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class SetViewShipmentTemplateObserver implements ObserverInterface
{
    /**
     * Temando provides additional shipment details compared to the default
     * carriers: origin location and documentation. Apply a custom template that
     * includes child blocks with these data items.
     * - event: layout_generate_blocks_after
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');
        if ($action !== 'adminhtml_order_shipment_view') {
            return;
        }

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getData('layout');
        $formBlock = $layout->getBlock('form');
        if (!$formBlock instanceof \Magento\Shipping\Block\Adminhtml\View\Form) {
            return;
        }

        $shippingMethod = $formBlock->getOrder()->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') === Carrier::CODE) {
            $formBlock->setTemplate('Temando_Shipping::shipping/view/form.phtml');
        }
    }
}
