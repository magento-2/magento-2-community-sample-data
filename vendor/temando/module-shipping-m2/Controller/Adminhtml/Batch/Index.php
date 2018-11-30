<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Batch;

use Magento\Framework\Controller\ResultFactory;
use Temando\Shipping\Controller\Adminhtml\Activation\AbstractRegisteredAction;

/**
 * Temando List Batches Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Index extends AbstractRegisteredAction
{
    const ADMIN_RESOURCE = 'Temando_Shipping::batches';

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::batches');
        $resultPage->getConfig()->getTitle()->prepend(__('Batches'));
        $resultPage->addBreadcrumb(__('Batches'), __('Batches'), $this->getUrl('temando/batch'));
        return $resultPage;
    }
}
