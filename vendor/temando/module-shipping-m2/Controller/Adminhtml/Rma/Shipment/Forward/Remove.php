<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Rma\Shipment\Forward;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Model\ResourceModel\Repository\RmaShipmentRepositoryInterface;

/**
 * Temando RMA Forward Fulfillment Shipment Remove Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Remove extends Action
{
    const ADMIN_RESOURCE = 'Magento_Rma::magento_rma';

    /**
     * @var RmaShipmentRepositoryInterface
     */
    private $rmaShipmentRepository;

    /**
     * @param Context $context
     * @param RmaShipmentRepositoryInterface $rmaShipmentRepository
     */
    public function __construct(
        Context $context,
        RmaShipmentRepositoryInterface $rmaShipmentRepository
    ) {
        $this->rmaShipmentRepository = $rmaShipmentRepository;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $rmaId = (int)$this->getRequest()->getParam('rma_id');
        $returnShipmentId = $this->getRequest()->getParam('ext_shipment_id');

        try {
            $this->rmaShipmentRepository->deleteShipmentIds($rmaId, [$returnShipmentId]);
        } catch (LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e, 'Shipment could not be removed from RMA.');
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultPage->setPath('adminhtml/rma/edit', [
            'id' => $rmaId,
            '_fragment' => 'rma_info_tabs_rma_shipments_content',
        ]);

        return $resultPage;
    }
}
