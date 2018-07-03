<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Rma\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;

/**
 * Temando RMA Shipment Track Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Track extends Action
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
     * @param Context $context
     * @param RmaAccess $rmaAccess
     * @param ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        Context $context,
        RmaAccess $rmaAccess,
        ShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->rmaAccess = $rmaAccess;
        $this->shipmentRepository = $shipmentRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        // load and register current RMA shipment
        $extShipmentId = $this->getRequest()->getParam('shipment_id');
        /** @var \Temando\Shipping\Model\ShipmentInterface $extShipment */
        $extShipment = $this->shipmentRepository->getById($extShipmentId);
        $this->rmaAccess->setCurrentRmaShipment($extShipment);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Tracking Information'));

        return $resultPage;
    }
}
