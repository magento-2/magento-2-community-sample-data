<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Settings\Advanced;

use Magento\Framework\Controller\ResultFactory;
use Temando\Shipping\Controller\Adminhtml\Activation\AbstractRegisteredAction;

/**
 * Temando Edit Advanced Settings Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Edit extends AbstractRegisteredAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Advanced Settings'));
        $resultPage->addBreadcrumb(
            __('Configuration'),
            __('Advanced Settings'),
            $this->getUrl('temando/settings_advanced/edit')
        );

        return $resultPage;
    }
}
