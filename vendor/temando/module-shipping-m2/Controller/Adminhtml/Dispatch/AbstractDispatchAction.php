<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Dispatch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Model\DispatchProviderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\DispatchRepositoryInterface;

/**
 * Temando List Dispatches Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
abstract class AbstractDispatchAction extends Action
{
    const ADMIN_RESOURCE = 'Temando_Shipping::dispatches';

    /**
     * @var DispatchRepositoryInterface
     */
    private $dispatchRepository;

    /**
     * @var DispatchProviderInterface
     */
    private $dispatchProvider;

    /**
     * View constructor.
     * @param Context $context
     * @param DispatchRepositoryInterface $dispatchRepository
     * @param DispatchProviderInterface $dispatchProvider
     */
    public function __construct(
        Context $context,
        DispatchRepositoryInterface $dispatchRepository,
        DispatchProviderInterface $dispatchProvider
    ) {
        $this->dispatchRepository = $dispatchRepository;
        $this->dispatchProvider = $dispatchProvider;

        parent::__construct($context);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|ResultInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $dispatchId = $this->getRequest()->getParam('dispatch_id');

        try {
            $dispatch = $this->dispatchRepository->getById($dispatchId);
            // register dispatch for use in output rendering
            $this->dispatchProvider->setDispatch($dispatch);
        } catch (NoSuchEntityException $e) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $resultForward->forward('noroute');
        }

        return parent::dispatch($request);
    }
}
