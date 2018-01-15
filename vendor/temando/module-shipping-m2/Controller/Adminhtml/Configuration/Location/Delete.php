<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Location;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Temando\Shipping\Model\ResourceModel\Repository\LocationRepositoryInterface;

/**
 * Temando Delete Location Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Delete extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::locations';

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * Temando Location Delete Action constructor.
     *
     * @param Context $context
     * @param LocationRepositoryInterface $locationRepository
     */
    public function __construct(Context $context, LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');

        $locationId = $this->getRequest()->getParam('location_id', false);
        if (!$locationId) {
            $this->messageManager->addErrorMessage(__('Location ID missing.'));

            return $resultRedirect;
        }

        try {
            $this->locationRepository->delete($locationId);
            $this->messageManager->addSuccessMessage(__('Location was deleted successfully.'));
        } catch (\Exception $e) {
            $message = __('An error occurred while deleting the location: %1', $e->getMessage());
            $this->messageManager->addExceptionMessage($e, $message);

            return $resultRedirect;
        }

        return $resultRedirect;
    }
}
