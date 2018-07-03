<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Location;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Temando\Shipping\Model\ResourceModel\Repository\LocationRepositoryInterface;
use Temando\Shipping\Ui\Component\MassAction\Filter;

/**
 * Temando Mass Delete Location Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::locations';

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * Temando Location Mass Delete Action constructor.
     *
     * @param Context $context
     * @param LocationRepositoryInterface $locationRepository
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        LocationRepositoryInterface $locationRepository,
        Filter $filter
    ) {
        $this->locationRepository = $locationRepository;
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

        $locationIds = $this->filter->getLocationIds($this->locationRepository, $selected, $excluded);
        $requestedItemsCount = count($locationIds);
        $deletedItemsCount = 0;

        foreach ($locationIds as $locationId) {
            try {
                $this->locationRepository->delete($locationId);
                $deletedItemsCount++;
            } catch (\Exception $e) {
                $message = __('Location %1 cannot be deleted: %2', $locationId, $e->getMessage());
                $this->messageManager->addExceptionMessage($e, $message);
            }
        }

        $resultMessage = __('A total of %1 record(s) have been deleted.', $deletedItemsCount);
        if ($requestedItemsCount !== $deletedItemsCount) {
            $this->messageManager->addWarningMessage($resultMessage);
            $errorMessage = 'An error occurred while deleting locations.';
            $errorMessage.= ' Please see the log files for more detailed information.';
            $this->messageManager->addErrorMessage(__($errorMessage));
        } else {
            $this->messageManager->addSuccessMessage($resultMessage);
        }

        return $resultRedirect;
    }
}
