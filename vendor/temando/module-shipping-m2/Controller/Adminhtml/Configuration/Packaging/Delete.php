<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Packaging;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Temando\Shipping\Model\ResourceModel\Repository\PackagingRepositoryInterface;

/**
 * Temando Delete Packaging Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Delete extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::packaging';

    /**
     * @var PackagingRepositoryInterface
     */
    private $packagingRepository;

    /**
     * Temando Packaging Delete Action constructor.
     *
     * @param Context $context
     * @param PackagingRepositoryInterface $packagingRepository
     */
    public function __construct(Context $context, PackagingRepositoryInterface $packagingRepository)
    {
        $this->packagingRepository = $packagingRepository;

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

        $packagingId = $this->getRequest()->getParam('packaging_id', false);
        if (!$packagingId) {
            $this->messageManager->addErrorMessage(__('Container ID missing.'));

            return $resultRedirect;
        }

        try {
            $this->packagingRepository->delete($packagingId);
            $this->messageManager->addSuccessMessage(__('Container was deleted successfully.'));
        } catch (\Exception $e) {
            $message = __('An error occurred while deleting the container: %1', $e->getMessage());
            $this->messageManager->addExceptionMessage($e, $message);

            return $resultRedirect;
        }

        return $resultRedirect;
    }
}
