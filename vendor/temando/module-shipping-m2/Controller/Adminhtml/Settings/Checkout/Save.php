<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Settings\Checkout;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * Save Checkout Settings Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Save extends Action
{
    /**
     * @var Json
     */
    private $decoder;

    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param Json $decoder
     * @param ModuleConfigInterface $moduleConfig
     */
    public function __construct(
        Context $context,
        Json $decoder,
        ModuleConfigInterface $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->decoder = $decoder;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Raw $rawResponse */
        $rawResponse = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        $fieldsDefinition = $this->getRequest()->getParam('fields', '[]');

        try {
            // sanitize input
            $this->decoder->unserialize($fieldsDefinition);
            $this->moduleConfig->saveCheckoutFieldsDefinition($fieldsDefinition);
            $rawResponse->setContents('OK');
        } catch (\InvalidArgumentException $e) {
            $rawResponse->setHttpResponseCode(422);
            $rawResponse->setContents('NOK');
        }

        return $rawResponse;
    }
}
