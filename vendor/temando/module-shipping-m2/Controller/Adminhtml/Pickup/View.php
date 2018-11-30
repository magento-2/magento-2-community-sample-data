<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Pickup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Temando\Shipping\Model\Pickup\PickupLoader;

/**
 * Temando View Pickup Page
 *
 * @package Temando\Shipping\Controller
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class View extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::pickups';

    /**
     * @var PickupLoader
     */
    private $pickupLoader;

    /**
     * View constructor.
     * @param Context $context
     * @param PickupLoader $pickupLoader
     */
    public function __construct(Context $context, PickupLoader $pickupLoader)
    {
        $this->pickupLoader = $pickupLoader;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('sales_order_id', 0);
        $pickupId = $this->getRequest()->getParam('pickup_id', '');

        $pickups = $this->pickupLoader->load($orderId, $pickupId);
        $this->pickupLoader->register($pickups, $orderId, $pickupId);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::pickups');
        $resultPage->getConfig()->getTitle()->prepend(__('Pickups'));
        $resultPage->addBreadcrumb(__('Pickups'), __('Pickups'), $this->getUrl('temando/pickup'));

        return $resultPage;
    }
}
