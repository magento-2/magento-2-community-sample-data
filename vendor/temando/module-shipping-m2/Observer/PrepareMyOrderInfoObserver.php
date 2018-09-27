<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\ViewModel\Order\Location;

/**
 * Change order info template for temando orders in customer account.
 *
 * @package Temando\Shipping\Observer
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class PrepareMyOrderInfoObserver implements ObserverInterface
{
    /**
     * @var Location
     */
    private $viewModel;

    /**
     * PrepareMyOrderInfoObserver constructor.
     * @param Location $viewModel
     */
    public function __construct(Location $viewModel)
    {
        $this->viewModel = $viewModel;
    }

    /**
     * Temando provides additional order details compared to the default carriers:
     * - collection point address.
     * Apply a custom template that displays these data items.
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
            'sales_order_invoice',
            'sales_order_creditmemo',
        ];
        $action = $observer->getData('full_action_name');
        if (!in_array($action, $applicableActions)) {
            return;
        }

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getData('layout');
        $infoBlock = $layout->getBlock('sales.order.info');
        if (!$infoBlock instanceof \Magento\Sales\Block\Order\Info) {
            return;
        }

        $order = $infoBlock->getOrder();
        if (!$order instanceof OrderInterface || !$order->getData('shipping_method')) {
            // wrong type, virtual or corrupt order
            return;
        }

        $shippingMethod = $order->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') !== Carrier::CODE) {
            return;
        }

        $infoBlock->setTemplate('Temando_Shipping::order/info.phtml');
        $infoBlock->setData('viewModel', $this->viewModel);
    }
}
