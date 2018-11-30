<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface as SalesShipmentRepositoryInterface;
use Magento\Sales\Model\Order\Shipment;
use Temando\Shipping\Model\BatchInterface;
use Temando\Shipping\Model\BatchProviderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\BatchRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\ViewModel\DataProvider\BatchUrl;

/**
 * Temando View Batch Action
 *
 * @package Temando\Shipping\Controller
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class View extends AbstractBatchAction
{
    /**
     * @var BatchUrl
     */
    private $batchUrl;

    /**
     * @var BatchProviderInterface
     */
    private $batchProvider;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentReferenceRepository;

    /**
     * @var SalesShipmentRepositoryInterface
     */
    private $salesShipmentRepository;

    /**
     * @var int
     */
    private $errorCounter = 0;

    /**
     * View constructor.
     * @param Context $context
     * @param BatchRepositoryInterface $batchRepository
     * @param BatchProviderInterface $batchProvider
     * @param BatchUrl $batchUrl
     * @param ShipmentRepositoryInterface $shipmentReferenceRepository
     * @param SalesShipmentRepositoryInterface $salesShipmentRepository
     */
    public function __construct(
        Context $context,
        BatchRepositoryInterface $batchRepository,
        BatchProviderInterface $batchProvider,
        BatchUrl $batchUrl,
        ShipmentRepositoryInterface $shipmentReferenceRepository,
        SalesShipmentRepositoryInterface $salesShipmentRepository
    ) {
        $this->batchUrl = $batchUrl;
        $this->batchProvider = $batchProvider;
        $this->shipmentReferenceRepository = $shipmentReferenceRepository;
        $this->salesShipmentRepository = $salesShipmentRepository;

        parent::__construct($context, $batchRepository, $batchProvider);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return (
            $this->_authorization->isAllowed(static::ADMIN_RESOURCE) &&
            $this->_authorization->isAllowed('Magento_Sales::shipment')
        );
    }

    /**
     * Render template.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $batch = $this->batchProvider->getBatch();
        $orders = $this->getOrdersForBatch($batch);
        $this->batchProvider->setOrders($orders);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Temando_Shipping::batches');
        $resultPage->getConfig()->getTitle()->prepend(__('Batches'));
        $resultPage->addBreadcrumb(__('Batches'), __('Batches'), $this->getUrl('temando/batch'));

        /** @var \Magento\Backend\Block\Template $toolbar */
        $toolbar = $this->_view->getLayout()->getBlock('page.actions.toolbar');
        $toolbar->addChild('back', Button::class, [
            'label' => __('Back'),
            'onclick' => sprintf("setLocation('%s')", $this->batchUrl->getListActionUrl()),
            'class' => 'back',
            'level' => 0
        ]);

        $orderIds = $this->getOrderIds($orders);
        $toolbar->addChild('print_packing_slips', Button::class, [
            'label' => __('Print All Packing Slips'),
            'onclick' => sprintf(
                "setLocation('%s')",
                $this->batchUrl->getPrintAllPackingSlips($orderIds, $batch->getBatchId())
            ),
            'class' => 'print',
            'level' => -1
        ]);

        return $resultPage;
    }

    /**
     * @param BatchInterface $batch
     * @return OrderInterface[]
     */
    private function getOrdersForBatch(BatchInterface $batch)
    {
        $orders = [];
        $batchShipments = $batch->getIncludedShipments();
        foreach ($batchShipments as $batchShipment) {
            $extShipmentId = $batchShipment->getShipmentId();

            try {
                $shipmentReference = $this->shipmentReferenceRepository->getReferenceByExtShipmentId($extShipmentId);
                /** @var Shipment $salesShipment */
                $salesShipment = $this->salesShipmentRepository->get($shipmentReference->getShipmentId());
                /** @var OrderInterface[] $orders */
                $orders[$extShipmentId] = $salesShipment->getOrder();
            } catch (LocalizedException $e) {
                $this->errorCounter++;
                if ($this->errorCounter === 1) {
                    $this->messageManager->addWarningMessage(
                        // @codingStandardsIgnoreLine
                        __('There are Shipments in this Batch that are still being created. Please check Back soon to view these Shipments.')
                    );
                }
            }
        }

        return $orders;
    }

    /**
     * @param OrderInterface[] $orders
     * @return string[]
     */
    private function getOrderIds(array $orders)
    {
        $orderIds = [];
        foreach ($orders as $order) {
            $orderIds[] = $order->getEntityId();
        }

        return $orderIds;
    }
}
