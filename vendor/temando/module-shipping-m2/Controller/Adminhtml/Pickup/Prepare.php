<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Pickup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Temando\Shipping\Model\Pickup\PickupLoader;
use Temando\Shipping\Model\Pickup\PickupManagementFactory;
use Temando\Shipping\Model\PickupInterface;

/**
 * Prepare Pickup Page
 *
 * @package Temando\Shipping\Controller
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Prepare extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::pickups';

    /**
     * @var PickupLoader
     */
    private $pickupLoader;

    /**
     * @var PickupManagementFactory
     */
    private $pickupManagementFactory;

    /**
     * Prepare constructor.
     *
     * @param Context $context
     * @param PickupLoader $pickupLoader
     * @param PickupManagementFactory $pickupManagementFactory
     */
    public function __construct(
        Context $context,
        PickupLoader $pickupLoader,
        PickupManagementFactory $pickupManagementFactory
    ) {
        $this->pickupLoader = $pickupLoader;
        $this->pickupManagementFactory = $pickupManagementFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('sales_order_id', 0);
        $pickupId = $this->getRequest()->getParam('pickup_id', '');

        // load pickups for current request parameters
        $pickups = $this->pickupLoader->load($orderId, $pickupId);

        // check if there is a pickup in "pickup requested" state amongst them
        $pickupManagement = $this->pickupManagementFactory->create([
            'pickups' => $pickups,
        ]);
        $requestedPickups = $pickupManagement->getPickupsByState(PickupInterface::STATE_REQUESTED);
        if (empty($requestedPickups)) {
            /** @var \Magento\Framework\Controller\Result\Forward $notFoundForward */
            $notFoundForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $notFoundForward->forward('noroute');

            $this->messageManager->addErrorMessage('No pickup fulfillment found to prepare.');

            return $notFoundForward;
        }

        // register pickups for further processing
        $requestedPickup = current($requestedPickups);
        $this->pickupLoader->register($pickups, $orderId, $requestedPickup->getPickupId());

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::pickups');
        $resultPage->getConfig()->getTitle()->prepend(__('Prepare for Pickup'));
        $resultPage->addBreadcrumb(__('Prepare for Pickup'), __('Prepare for Pickup'), $this->getUrl('temando/pickup'));

        return $resultPage;
    }
}
