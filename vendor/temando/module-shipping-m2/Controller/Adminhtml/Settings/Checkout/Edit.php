<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Settings\Checkout;

use Magento\Framework\Controller\ResultFactory;
use Temando\Shipping\Controller\Adminhtml\Activation\AbstractRegisteredAction;

/**
 * Edit Checkout Settings Action
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
        $resultPage->setActiveMenu('Magento_Config::system_config');

        $resultPage->getConfig()->getTitle()->prepend(__('Configuration'));
        $resultPage->getConfig()->getTitle()->prepend(__('Checkout View Settings'));

        $resultPage->addBreadcrumb(
            __('Checkout View Settings'),
            __('Checkout View Settings'),
            $this->getUrl('temando/settings_checkout/edit')
        );

        return $resultPage;
    }
}
