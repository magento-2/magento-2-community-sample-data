<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Controller\Adminhtml\Batch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Model\BatchProviderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\BatchRepositoryInterface;

/**
 * Temando Batch Action
 *
 * Register a batch as referenced via request parameter
 *
 * @package  Temando\Shipping\Controller
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
abstract class AbstractBatchAction extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::batches';

    /**
     * @var BatchRepositoryInterface
     */
    private $batchRepository;

    /**
     * @var BatchProviderInterface
     */
    private $batchProvider;

    /**
     * AbstractBatchAction constructor.
     * @param Context $context
     * @param BatchRepositoryInterface $batchRepository
     * @param BatchProviderInterface $batchProvider
     */
    public function __construct(
        Context $context,
        BatchRepositoryInterface $batchRepository,
        BatchProviderInterface $batchProvider
    ) {
        $this->batchRepository = $batchRepository;
        $this->batchProvider = $batchProvider;

        parent::__construct($context);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|ResultInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $batchId = $request->getParam('batch_id');

        try {
            $batch = $this->batchRepository->getById($batchId);
            // register batch for use in output rendering
            $this->batchProvider->setBatch($batch);
        } catch (NoSuchEntityException $e) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $resultForward->forward('noroute');
        }

        return parent::dispatch($request);
    }
}
