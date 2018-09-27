<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Carrier;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Temando Available Carriers Page
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class NewAction extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::carriers';

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::carriers');

        $resultPage->getConfig()->getTitle()->prepend(__('Carriers'));
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Partners'));

        $resultPage->addBreadcrumb(__('Carriers'), __('Carriers'), $this->getUrl('temando/configuration_carrier'));
        $resultPage->addBreadcrumb(__('Shipping Partners'), __('Shipping Partners'));

        return $resultPage;
    }
}
