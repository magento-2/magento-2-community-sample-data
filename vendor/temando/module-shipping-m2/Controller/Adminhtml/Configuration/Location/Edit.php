<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Location;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Temando Edit Location Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Edit extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::locations';

    /**
     * Edit location
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::locations');

        $resultPage->getConfig()->getTitle()->prepend(__('Locations'));
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Location'));

        $resultPage->addBreadcrumb(__('Locations'), __('Locations'), $this->getUrl('temando/configuration_location'));
        $resultPage->addBreadcrumb(__('Edit Location'), __('Edit Location'));

        return $resultPage;
    }
}
