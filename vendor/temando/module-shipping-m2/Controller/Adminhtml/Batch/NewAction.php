<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Ui\Component\MassAction\Filter;
use Temando\Shipping\Controller\Adminhtml\Activation\AbstractRegisteredAction;
use Temando\Shipping\Model\BatchProviderInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Batch\OrderCollection;
use Temando\Shipping\Model\ResourceModel\Batch\OrderCollectionFactory;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Temando Add New Batch Action for MassCreate Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class NewAction extends AbstractRegisteredAction
{
    const ADMIN_RESOURCE = 'Temando_Shipping::batches';

    /**
     * @var OrderCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var BatchProviderInterface
     */
    private $batchProvider;

    /**
     * @param Context                $context
     * @param ModuleConfigInterface  $config
     * @param BatchProviderInterface $batchProvider
     * @param OrderCollectionFactory $collectionFactory
     * @param Filter                 $massActionFilter
     */
    public function __construct(
        Context $context,
        ModuleConfigInterface $config,
        BatchProviderInterface $batchProvider,
        OrderCollectionFactory $collectionFactory,
        Filter $massActionFilter
    ) {
        $this->batchProvider     = $batchProvider;
        $this->collectionFactory = $collectionFactory;
        $this->massActionFilter  = $massActionFilter;
        $this->config            = $config;
        parent::__construct($context, $config);
    }

    /**
     * Prepare order collection as indicated via request params. After that it renders the BatchCreate component.
     * The orders listing mass action forward here.
     * For the batch listing action a different new controller might be needed
     *
     * - On success, creating Batch and redirecting to the batch grid page.
     * - If no orders ready for processing found, redirect back with error message
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $redirectRoute = 'sales/order/index';

        $collection = $this->collectionFactory->create();
        /** @var OrderCollection $orderCollection */
        $orderCollection = $this->massActionFilter->getCollection($collection);
        $orderCollection->addCarrierFilter(Carrier::CODE);
        $orderCollection->addCanShipFilter();

        $orders = array_filter($orderCollection->getItems(), function (Order $order) {
            return $order->canShip();
        });

        $hasError = false;
        if (empty($orders)) {
            $this->messageManager->addErrorMessage('None of the orders you selected are valid for batch processing.');
            $hasError = true;
        }
        if ((!$this->config->isSyncEnabled()) || (!$this->config->isSyncShipmentEnabled())) {
            $this->messageManager->addErrorMessage('You must enable Event Sync for shipments to use batch processing.');
            $hasError = true;
        }

        if ($hasError) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($redirectRoute);

            return $resultRedirect;
        }

        $this->batchProvider->setOrders($orders);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::batches');

        $resultPage->getConfig()->getTitle()->prepend(__('Batches'));
        $resultPage->getConfig()->getTitle()->prepend(__('Create New Batch'));

        $resultPage->addBreadcrumb(__('Batches'), __('Batches'), $this->getUrl('temando/batch'));
        $resultPage->addBreadcrumb(__('Create New Batch'), __('Create New Batch'));

        return $resultPage;
    }
}
