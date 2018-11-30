<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Dispatch;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Model\DispatchInterface;
use Temando\Shipping\Model\ResourceModel\Repository\DispatchRepositoryInterface;
use Temando\Shipping\Rest\Adapter\CompletionApiInterface;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\EntityMapper\DispatchResponseMapper;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\ListRequestInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\CompletionResponseType;

/**
 * Temando Dispatch Repository
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchRepository implements DispatchRepositoryInterface
{
    /**
     * @var CompletionApiInterface
     */
    private $apiAdapter;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $completionRequestFactory;

    /**
     * @var ListRequestInterfaceFactory
     */
    private $completionsRequestFactory;

    /**
     * @var DispatchResponseMapper
     */
    private $dispatchMapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DispatchRepository constructor.
     * @param CompletionApiInterface $apiAdapter
     * @param ItemRequestInterfaceFactory $completionRequestFactory
     * @param ListRequestInterfaceFactory $completionsRequestFactory
     * @param DispatchResponseMapper $dispatchMapper
     * @param LoggerInterface $logger
     */
    public function __construct(
        CompletionApiInterface $apiAdapter,
        ItemRequestInterfaceFactory $completionRequestFactory,
        ListRequestInterfaceFactory $completionsRequestFactory,
        DispatchResponseMapper $dispatchMapper,
        LoggerInterface $logger
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->completionRequestFactory = $completionRequestFactory;
        $this->completionsRequestFactory = $completionsRequestFactory;
        $this->dispatchMapper = $dispatchMapper;
        $this->logger = $logger;
    }

    /**
     * @param string $dispatchId
     * @return DispatchInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getById($dispatchId)
    {
        if (!$dispatchId) {
            throw new LocalizedException(__('An error occurred while loading data.'));
        }

        try {
            $request = $this->completionRequestFactory->create(['entityId' => $dispatchId]);
            $apiCompletion = $this->apiAdapter->getCompletion($request);
            $dispatch = $this->dispatchMapper->map($apiCompletion);
        } catch (AdapterException $e) {
            if ($e->getCode() === 404) {
                throw NoSuchEntityException::singleField('completionId', $dispatchId);
            }

            throw new LocalizedException(__('An error occurred while loading data.'), $e);
        }

        return $dispatch;
    }

    /**
     * @param int|null $offset
     * @param int|null $limit
     * @return DispatchInterface[]
     */
    public function getList($offset = null, $limit = null)
    {
        $fetchDispatchesFrom = time() - (90*24*60*60);
        $filter = [
            'path' => '/createdAt',
            'operator' => 'greaterThanOrEqual',
            'value' => date('c', $fetchDispatchesFrom),
        ];
        $filterGroup = [$filter];

        try {
            $request = $this->completionsRequestFactory->create([
                'offset' => $offset,
                'limit' => $limit,
                'filter' => $filterGroup,
            ]);
            $apiCompletions = $this->apiAdapter->getCompletions($request);
            $dispatches = array_map(function (CompletionResponseType $apiCompletion) {
                return $this->dispatchMapper->map($apiCompletion);
            }, $apiCompletions);
        } catch (AdapterException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $dispatches = [];
        }

        return $dispatches;
    }
}
