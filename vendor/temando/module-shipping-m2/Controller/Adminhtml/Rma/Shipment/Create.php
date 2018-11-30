<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Rma\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Temando\Shipping\Controller\Adminhtml\Activation\AbstractRegisteredAction;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;

/**
 * Temando Create RMA Shipment Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Create extends AbstractRegisteredAction
{
    const ADMIN_RESOURCE = 'Magento_Rma::magento_rma';

    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * Create constructor.
     * @param Context $context
     * @param ModuleConfigInterface $config
     * @param RmaAccess $rmaAccess
     */
    public function __construct(
        Context $context,
        ModuleConfigInterface $config,
        RmaAccess $rmaAccess
    ) {
        $this->rmaAccess = $rmaAccess;

        parent::__construct($context, $config);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rmaId = $this->getRequest()->getParam('rma_id');

        // load and register current RMA
        $rma = $this->rmaAccess->getById($rmaId);
        $this->rmaAccess->setCurrentRma($rma);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Create Return Shipment'));

        return $resultPage;
    }
}
