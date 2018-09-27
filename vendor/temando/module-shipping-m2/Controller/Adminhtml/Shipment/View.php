<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Escaper;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;

/**
 * Temando Redirect Shipment Page
 *
 * Query a Shipment ID based on given Platform ID and redirect to native shipment page.
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class View extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::shipping';

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * View constructor.
     *
     * @param Context                     $context
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param Escaper                     $escaper
     */
    public function __construct(
        Context $context,
        ShipmentRepositoryInterface $shipmentRepository,
        Escaper $escaper
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->escaper            = $escaper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\AbstractResult
     */
    public function execute()
    {
        $extShipmentId = $this->escaper->escapeHtml($this->getRequest()->getParam('shipment_id'));

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $shipmentId = $this->shipmentRepository->getReferenceByExtShipmentId($extShipmentId)->getShipmentId();
            $resultRedirect->setPath('sales/shipment/view', ['shipment_id' => $shipmentId]);
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $message = "Shipment '$extShipmentId' not found.";
            $this->messageManager->addExceptionMessage($exception, __($message));

            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');

            return $resultForward;
        }

        return $resultRedirect;
    }
}
