<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Rma\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\ShipmentRepositoryInterface as SalesShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;

/**
 * Temando View Return Shipment Action
 *
 * A return shipment can be either accessed
 * - from the RMA Edit page with an `rma_id` request parameter available ("Added Shipment") OR
 * - from the Shipment View page with a `shipment_id` request parameter available ("Available Shipment")
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class View extends Action
{
    const ADMIN_RESOURCE = 'Magento_Rma::magento_rma';

    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var SalesShipmentRepositoryInterface
     */
    private $salesShipmentRepository;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param RmaAccess $rmaAccess
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param SalesShipmentRepositoryInterface $salesShipmentRepository
     */
    public function __construct(
        Context $context,
        RmaAccess $rmaAccess,
        ShipmentRepositoryInterface $shipmentRepository,
        SalesShipmentRepositoryInterface $salesShipmentRepository
    ) {
        $this->rmaAccess = $rmaAccess;
        $this->shipmentRepository = $shipmentRepository;
        $this->salesShipmentRepository = $salesShipmentRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $rmaId = $this->getRequest()->getParam('rma_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        if (!$rmaId && !$shipmentId) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultPage->forward('noroute');
            return $resultPage;
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        if ($rmaId) {
            // load existing RMA
            $rma = $this->rmaAccess->getById($rmaId);

            $resultPage->addHandle('temando_rma_shipment_view_added');
        } else {
            // create dummy RMA
            $salesShipment = $this->salesShipmentRepository->get($shipmentId);
            $rma = $this->rmaAccess->create(['data' => [
                'store_id' => $salesShipment->getStoreId(),
                'customer_id' => $salesShipment->getCustomerId(),
                'order_id' => $salesShipment->getOrderId(),
            ]]);

            $resultPage->addHandle('temando_rma_shipment_view_available');
        }

        // register current RMA
        $this->rmaAccess->setCurrentRma($rma);

        // load and register current RMA shipment
        $extShipmentId = $this->getRequest()->getParam('ext_shipment_id');
        /** @var \Temando\Shipping\Model\ShipmentInterface $extShipment */
        $extShipment = $this->shipmentRepository->getById($extShipmentId);
        $this->rmaAccess->setCurrentRmaShipment($extShipment);

        $resultPage->getConfig()->getTitle()->prepend(__('Return Shipment'));
        return $resultPage;
    }
}
