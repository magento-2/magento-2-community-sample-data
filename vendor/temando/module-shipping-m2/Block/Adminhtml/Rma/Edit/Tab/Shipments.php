<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Rma\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * RMA Shipments Tab
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class Shipments extends Widget implements TabInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Context               $context
     * @param Registry              $registry
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * Get Header Text for Order Selection
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Return Shipments');
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Return Shipments');
    }

    /**
     * Prepare tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return true
     */
    public function canShowTab()
    {
        // only display if original order was shipped with temando shipping
        /** @var Order $order */
        $order = $this->registry->registry('current_order');
        if (!$order instanceof OrderInterface || !$order->getData('shipping_method')) {
            // wrong type, virtual or corrupt order
            return false;
        }

        $shippingMethod = $order->getShippingMethod(true);
        $carrierCode    = $shippingMethod->getData('carrier_code');

        return ($carrierCode === Carrier::CODE);
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return !$this->canShowTab();
    }
}
