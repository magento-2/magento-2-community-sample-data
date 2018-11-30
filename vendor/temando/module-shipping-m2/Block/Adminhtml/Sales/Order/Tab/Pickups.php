<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Sales\Order\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Text\ListText;
use Temando\Shipping\Model\Pickup\PickupLoader;
use Temando\Shipping\Model\Pickup\PickupManagementFactory;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\ViewModel\DataProvider\PickupUrl;
use Temando\Shipping\ViewModel\Order\OrderDetails;

/**
 * Sales Order Pickup Tab
 *
 * @api
 * @package Temando\Shipping\Block
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 *
 */
class Pickups extends ListText implements TabInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var OrderDetails
     */
    private $orderDetails;

    /**
     * @var PickupUrl
     */
    private $pickupUrl;

    /**
     * @var PickupLoader
     */
    private $pickupLoader;

    /**
     * @var PickupManagementFactory
     */
    private $pickupManagementFactory;

    /**
     * Pickup constructor.
     * @param Context $context
     * @param Registry $registry
     * @param OrderDetails $orderDetails
     * @param PickupUrl $pickupUrl
     * @param PickupLoader $pickupLoader
     * @param PickupManagementFactory $pickupManagementFactory
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderDetails $orderDetails,
        PickupUrl $pickupUrl,
        PickupLoader $pickupLoader,
        PickupManagementFactory $pickupManagementFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->orderDetails = $orderDetails;
        $this->pickupUrl = $pickupUrl;
        $this->pickupLoader = $pickupLoader;
        $this->pickupManagementFactory = $pickupManagementFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * Get Header Text for Order Selection
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Pickups');
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Pickups');
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
        $order = $this->getOrder();
        return $this->orderDetails->isPickupOrder($order);
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

    /**
     * @return ListText
     * @throws LocalizedException
     */
    public function _prepareLayout()
    {
        $order = $this->getOrder();
        if (!$order->canShip() || !$this->orderDetails->isPickupOrder($order)) {
            // do not add "Prepare for Pickup" button if all items were shipped.
            return parent::_prepareLayout();
        }

        // load all pickups associated to the current order
        $pickups = $this->pickupLoader->load((int)$order->getId());
        $pickupManagement = $this->pickupManagementFactory->create([
            'pickups' => $pickups,
        ]);

        $requestedPickups = $pickupManagement->getPickupsByState(PickupInterface::STATE_REQUESTED);
        if (empty($requestedPickups)) {
            // do not add "Prepare for Pickup" button if no pending pickup fulfillments exist.
            return parent::_prepareLayout();
        }

        /** @var PickupInterface $requestedPickup */
        $requestedPickup = current($requestedPickups);
        $prepareForPickupUrl = $this->pickupUrl->getEditActionUrl([
            'sales_order_id' => $order->getId(),
            'pickup_id' => $requestedPickup->getPickupId()
        ]);

        /** @var \Magento\Framework\View\Element\Template $toolbar */
        $toolbar = $this->getLayout()->getBlock('page.actions.toolbar');
        $toolbar->addChild('pickup_prepare', Button::class, [
            'label'   => __('Prepare for Pickup'),
            'onclick' => sprintf("setLocation('%s')", $prepareForPickupUrl),
        ]);

        return parent::_prepareLayout();
    }
}
