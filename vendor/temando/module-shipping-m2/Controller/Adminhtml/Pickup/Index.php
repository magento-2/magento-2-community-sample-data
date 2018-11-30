<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Pickup;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * List Pickups Page
 *
 * @package Temando\Shipping\Controller
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Index extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::pickups';

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::pickups');
        $resultPage->getConfig()->getTitle()->prepend(__('Pickups'));
        $resultPage->addBreadcrumb(__('Pickups'), __('Pickups'), $this->getUrl('temando/pickup'));

        return $resultPage;
    }
}
