<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Dispatch;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Model\DispatchInterface;
use Temando\Shipping\Model\ResourceModel\Repository\DispatchRepositoryInterface;
use Temando\Shipping\Rest\Adapter\CompletionApiInterface;
use Temando\Shipping\Rest\EntityMapper\DispatchResponseMapper;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;

/**
 * Temando Dispatch Repository
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.temando.com/
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
     * @var DispatchResponseMapper
     */
    private $dispatchMapper;

    /**
     * DispatchRepository constructor.
     * @param CompletionApiInterface $apiAdapter
     * @param ItemRequestInterfaceFactory $completionRequestFactory
     * @param DispatchResponseMapper $dispatchMapper
     */
    public function __construct(
        CompletionApiInterface $apiAdapter,
        ItemRequestInterfaceFactory $completionRequestFactory,
        DispatchResponseMapper $dispatchMapper
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->completionRequestFactory = $completionRequestFactory;
        $this->dispatchMapper = $dispatchMapper;
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
}
