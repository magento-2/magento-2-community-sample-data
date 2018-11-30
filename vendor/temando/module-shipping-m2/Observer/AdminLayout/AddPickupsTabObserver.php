<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer\AdminLayout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Add pickups tab for temando orders.
 *
 * @package Temando\Shipping\Observer
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class AddPickupsTabObserver implements ObserverInterface
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
     * Append an additional tab block if the current order is a Temando order.
     *
     * - event: layout_load_before
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $applicableActions = [
            'sales_order_view',
        ];
        $action = $observer->getData('full_action_name');
        if (!in_array($action, $applicableActions)) {
            // not the order details page
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->registry->registry('current_order');
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
        $layout->getUpdate()->addHandle('temando_order_pickups_tab');
    }
}
