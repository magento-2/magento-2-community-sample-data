<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Carrier;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Temando\Shipping\Model\ResourceModel\Repository\CarrierRepositoryInterface;

/**
 * Temando Delete Carrier Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Delete extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::carriers';

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * Temando Carrier Delete Action constructor.
     *
     * @param Context $context
     * @param CarrierRepositoryInterface $carrierRepository
     */
    public function __construct(Context $context, CarrierRepositoryInterface $carrierRepository)
    {
        $this->carrierRepository = $carrierRepository;

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

        $carrierConfigurationId = $this->getRequest()->getParam('configuration_id', false);
        if (!$carrierConfigurationId) {
            $this->messageManager->addErrorMessage(__('Carrier ID missing.'));

            return $resultRedirect;
        }

        try {
            $this->carrierRepository->delete($carrierConfigurationId);
            $this->messageManager->addSuccessMessage(__('Carrier was deleted successfully.'));
        } catch (\Exception $e) {
            $message = __('An error occurred while deleting the carrier: %1', $e->getMessage());
            $this->messageManager->addExceptionMessage($e, $message);

            return $resultRedirect;
        }

        return $resultRedirect;
    }
}
