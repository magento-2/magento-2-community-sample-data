<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Block\Adminhtml\Order\View\Tab\Info;
use Magento\Shipping\Block\Adminhtml\Create\Form;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\ViewModel\Order\Location;
use Temando\Shipping\ViewModel\Order\OrderDetails;

/**
 * Change order view template for temando orders.
 *
 * @package Temando\Shipping\Observer
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class PrepareOrderViewObserver implements ObserverInterface
{
    /**
     * @var Location
     */
    private $locationViewModel;

    /**
     * @var OrderDetails
     */
    private $orderDetailsViewModel;

    /**
     * PrepareShipmentViewObserver constructor.
     * @param Location $locationViewModel
     * @param OrderDetails $orderDetailsViewModel
     */
    public function __construct(
        Location $locationViewModel,
        OrderDetails $orderDetailsViewModel
    ) {
        $this->locationViewModel = $locationViewModel;
        $this->orderDetailsViewModel = $orderDetailsViewModel;
    }

    /**
     * Temando provides additional order details compared to the default carriers:
     * - external order reference
     * - collection point address
     * Apply a custom template that includes these – locally available – data items.
     *
     * - event: layout_generate_blocks_after
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $applicableActions = [
            'sales_order_view',
            'adminhtml_order_shipment_new',
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

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getData('layout');
        $infoBlock = $layout->getBlock('order_info');
        if (!$infoBlock instanceof \Magento\Sales\Block\Adminhtml\Order\View\Info) {
            return;
        }

        /** @var Info|Form $parentBlock */
        $parentBlock = $layout->getBlock('order_tab_info') ?: $layout->getBlock('form');
        $order = $parentBlock->getOrder();
        if (!$order instanceof OrderInterface || !$order->getData('shipping_method')) {
            // wrong type, virtual or corrupt order
            return;
        }

        $shippingMethod = $order->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') !== Carrier::CODE) {
            return;
        }

        // set template to render external entity references and addresses from platform
        $infoBlock->setTemplate('Temando_Shipping::sales/order/view/info.phtml');
        $infoBlock->setData('locationViewModel', $this->locationViewModel);
        $infoBlock->setData('orderDetailsViewModel', $this->orderDetailsViewModel);
    }
}
