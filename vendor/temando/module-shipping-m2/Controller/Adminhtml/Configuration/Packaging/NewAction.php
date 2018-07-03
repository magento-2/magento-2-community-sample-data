<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Packaging;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Temando New Packaging Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class NewAction extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::packaging';

    /**
     * Create new packaging
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::packaging');

        $resultPage->getConfig()->getTitle()->prepend(__('Packaging'));
        $resultPage->getConfig()->getTitle()->prepend(__('Add a Package'));

        $resultPage->addBreadcrumb(__('Packaging'), __('Packaging'), $this->getUrl('temando/configuration_packaging'));
        $resultPage->addBreadcrumb(__('Add a Package'), __('Add a Package'));

        return $resultPage;
    }
}
