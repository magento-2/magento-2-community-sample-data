<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Rma\RmaShipment\View;

use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Temando\Shipping\Block\Adminhtml\Sales\Order\View\Info as SalesOrderInfo;
use Temando\Shipping\Model\ResourceModel\Order\OrderRepository;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;

/**
 * RMA Shipment General Info
 *
 * @api
 * @package  Temando\Shipping\Block
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @deprecated since 1.1.3 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Order\CustomerDetails
 * @see \Temando\Shipping\ViewModel\Order\OrderDetails
 * @see \Temando\Shipping\ViewModel\Rma\RmaView
 *
 * @method \Magento\Sales\Api\Data\OrderInterface getOrder()
 * @method void setOrder() setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
 */
class Info extends SalesOrderInfo
{
    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * Info constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory $elementFactory
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param ShipmentProviderInterface $shipmentProvider
     * @param OrderAddressInterfaceFactory $addressFactory
     * @param OrderRepository $orderRepository
     * @param RmaAccess $rmaAccess
     * @param mixed[] $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        ShipmentProviderInterface $shipmentProvider,
        OrderAddressInterfaceFactory $addressFactory,
        OrderRepository $orderRepository,
        RmaAccess $rmaAccess,
        $data = []
    ) {
        $this->rmaAccess = $rmaAccess;

        parent::__construct(
            $context,
            $registry,
            $adminHelper,
            $groupRepository,
            $metadata,
            $elementFactory,
            $addressRenderer,
            $shipmentProvider,
            $addressFactory,
            $orderRepository,
            $data
        );
    }

    /**
     * Set the order model for use in parent class.
     *
     * @see \Magento\Sales\Block\Adminhtml\Order\View\Info::getOrderStoreName
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    protected function _beforeToHtml()
    {
        /** @var \Magento\Rma\Model\Rma $rma */
        $rma = $this->getData('viewModel')->getRma();
        $order = $rma->getOrder();

        $this->setOrder($order);

        return $this;
    }
}
