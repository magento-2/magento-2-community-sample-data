<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\Controller\ResultFactory;
use Temando\Shipping\Model\BatchInterface;
use Temando\Shipping\Model\BatchProviderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\BatchRepositoryInterface;
use Temando\Shipping\ViewModel\DataProvider\BatchUrl;
use Magento\Backend\Model\View\Result\Page;

/**
 * Temando Solve Batch Failures Action
 *
 * @package Temando\Shipping\Controller
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Solve extends AbstractBatchAction
{
    /**
     * @var BatchProviderInterface
     */
    private $batchProvider;

    /**
     * @var BatchUrl
     */
    private $batchUrl;

    /**
     * AbstractBatchAction constructor.
     * @param Context $context
     * @param BatchRepositoryInterface $batchRepository
     * @param BatchProviderInterface $batchProvider
     * @param BatchUrl $batchUrl
     */
    public function __construct(
        Context $context,
        BatchRepositoryInterface $batchRepository,
        BatchProviderInterface $batchProvider,
        BatchUrl $batchUrl
    ) {
        $this->batchProvider = $batchProvider;
        $this->batchUrl = $batchUrl;
        parent::__construct($context, $batchRepository, $batchProvider);
    }

    /**
     * Render template.
     *
     * @return Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::batches');
        $resultPage->getConfig()->getTitle()->prepend(__('Find Solutions'));
        $resultPage->addBreadcrumb(__('Batches'), __('Batches'), $this->getUrl('temando/batch'));
        $resultPage->addBreadcrumb(__('Find Solutions'), __('Find Solutions'));

        /** @var \Magento\Backend\Block\Template $toolbar */
        $toolbar = $this->_view->getLayout()->getBlock('page.actions.toolbar');
        $toolbar->addChild('back', Button::class, [
            'label' => __('Back'),
            'onclick' => sprintf("setLocation('%s')", $this->batchUrl->getViewActionUrl([
                BatchInterface::BATCH_ID => $this->batchProvider->getBatch()->getBatchId(),
            ])),
            'class' => 'back',
            'level' => -1
        ]);

        return $resultPage;
    }
}
