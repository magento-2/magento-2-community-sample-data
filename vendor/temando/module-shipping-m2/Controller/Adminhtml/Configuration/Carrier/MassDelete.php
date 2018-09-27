<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Carrier;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Temando\Shipping\Model\ResourceModel\Repository\CarrierRepositoryInterface;
use Temando\Shipping\Ui\Component\MassAction\Filter;

/**
 * Temando Mass Delete Carrier Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::carriers';

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * Temando Carrier Mass Delete Action constructor.
     *
     * @param Context $context
     * @param CarrierRepositoryInterface $carrierRepository
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CarrierRepositoryInterface $carrierRepository,
        Filter $filter
    ) {
        $this->carrierRepository = $carrierRepository;
        $this->filter = $filter;

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

        $selected = $this->getRequest()->getParam(\Magento\Ui\Component\MassAction\Filter::SELECTED_PARAM, []);
        $excluded = $this->getRequest()->getParam(\Magento\Ui\Component\MassAction\Filter::EXCLUDED_PARAM, []);
        if ($excluded === 'false') {
            $excluded = [];
        }

        $carrierIds = $this->filter->getCarrierIds($this->carrierRepository, $selected, $excluded);
        $requestedItemsCount = count($carrierIds);
        $deletedItemsCount = 0;

        foreach ($carrierIds as $carrierId) {
            try {
                $this->carrierRepository->delete($carrierId);
                $deletedItemsCount++;
            } catch (\Exception $e) {
                $message = __('Carrier %1 cannot be deleted: %2', $carrierId, $e->getMessage());
                $this->messageManager->addExceptionMessage($e, $message);
            }
        }

        $resultMessage = __('A total of %1 record(s) have been deleted.', $deletedItemsCount);
        if ($requestedItemsCount !== $deletedItemsCount) {
            $this->messageManager->addWarningMessage($resultMessage);
            $errorMessage = 'An error occurred while deleting carriers.';
            $errorMessage.= ' Please see the log files for more detailed information.';
            $this->messageManager->addErrorMessage(__($errorMessage));
        } else {
            $this->messageManager->addSuccessMessage($resultMessage);
        }

        return $resultRedirect;
    }
}
