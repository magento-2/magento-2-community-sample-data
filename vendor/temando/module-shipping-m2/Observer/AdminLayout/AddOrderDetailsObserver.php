<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer\AdminLayout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Change order info template for Temando orders
 *
 * @package Temando\Shipping\Observer
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class AddOrderDetailsObserver implements ObserverInterface
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
     * @return OrderInterface
     */
    private function getOrder()
    {
        $creditmemo = $this->registry->registry('current_creditmemo');
        if ($creditmemo instanceof CreditmemoInterface) {
            return $creditmemo->getOrder();
        }

        $invoice = $this->registry->registry('current_invoice');
        if ($invoice instanceof InvoiceInterface) {
            return $invoice->getOrder();
        }

        return $this->registry->registry('current_order');
    }

    /**
     * Temando provides additional order details compared to the default carriers:
     * - external order reference
     * - collection point address
     * Apply a custom template that includes these – locally available – data items.
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
            'sales_order_invoice_new',
            'sales_order_invoice_view',
            'sales_order_creditmemo_new',
            'sales_order_creditmemo_view',
        ];

        $action = $observer->getData('full_action_name');
        if (!in_array($action, $applicableActions)) {
            // not the order details page
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();
        if (!$order instanceof OrderInterface || !$order->getData('shipping_method')) {
            return;
        }

        $shippingMethod = $order->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') !== Carrier::CODE) {
            return;
        }

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getData('layout');
        $layout->getUpdate()->addHandle('temando_order_info');
    }
}
