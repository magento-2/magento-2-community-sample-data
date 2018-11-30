<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Pickup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Temando\Shipping\Model\Pickup\Pdf\PickupPdfFactory;
use Temando\Shipping\Model\Pickup\PickupLoader;
use Temando\Shipping\Model\PickupProviderInterface;

/**
 * Temando Print Action
 *
 * @package Temando\Shipping\Controller
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PrintAction extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Temando_Shipping::pickups';

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var PickupPdfFactory
     */
    private $pickupPdfFactory;

    /**
     * @var PickupLoader
     */
    private $pickupLoader;

    /**
     * @var PickupProviderInterface
     */
    private $pickupProvider;

    /**
     * @param Context $context
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param PickupPdfFactory $pickupPdfFactory
     * @param PickupLoader $pickupLoader
     * @param PickupProviderInterface $pickupProvider
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        FileFactory $fileFactory,
        PickupPdfFactory $pickupPdfFactory,
        PickupLoader $pickupLoader,
        PickupProviderInterface $pickupProvider
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pickupPdfFactory = $pickupPdfFactory;
        $this->pickupLoader = $pickupLoader;
        $this->pickupProvider = $pickupProvider;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect
     */
    public function execute()
    {
        $pickupId = $this->getRequest()->getParam('pickup_id');
        $orderId = $this->getRequest()->getParam('sales_order_id');
        try {
            return $this->createPackagingSlip($pickupId, $orderId);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was an error creating package slip pdf.'));

            return $this->resultRedirectFactory->create()->setPath($this->_redirect->getRefererUrl());
        }
    }

    /**
     * @param string $pickupId
     * @param string $orderId
     *
     * @return ResponseInterface|Redirect
     * @throws \Exception
     */
    private function createPackagingSlip($pickupId, $orderId)
    {
        $pickups = $this->pickupLoader->load((int)$orderId, (string)$pickupId);
        $this->pickupLoader->register($pickups, (int)$orderId, (string)$pickupId);

        $pickup = $this->pickupProvider->getPickup();
        $order = $this->pickupProvider->getOrder();

        $pickupPdf = $this->pickupPdfFactory->create(
            ['data' => ['order' => $order, 'pickup' => $pickup, 'pickups' => $pickups]]
        );
        $filename = sprintf(
            'packingslip-%s-%s.pdf',
            $pickupId,
            $this->dateTime->date('Y-m-d_H-i-s')
        );
        $content = $pickupPdf->getPdf()->render();
        $response = $this->fileFactory->create(
            $filename,
            $content,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );

        return $response;
    }
}
