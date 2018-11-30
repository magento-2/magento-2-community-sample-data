<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Batch;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Model\BatchInterface;
use Temando\Shipping\Model\ResourceModel\Repository\BatchRepositoryInterface;
use Temando\Shipping\Rest\Adapter\BatchApiInterface;
use Temando\Shipping\Rest\EntityMapper\BatchResponseMapper;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;

/**
 * Temando Batch Repository
 *
 * @package  Temando\Shipping\Model
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class BatchRepository implements BatchRepositoryInterface
{
    /**
     * @var BatchApiInterface
     */
    private $apiAdapter;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $batchRequestFactory;

    /**
     * @var BatchResponseMapper
     */
    private $batchMapper;

    /**
     * BatchRepository constructor.
     * @param BatchApiInterface $apiAdapter
     * @param ItemRequestInterfaceFactory $batchRequestFactory
     * @param BatchResponseMapper $batchMapper
     */
    public function __construct(
        BatchApiInterface $apiAdapter,
        ItemRequestInterfaceFactory $batchRequestFactory,
        BatchResponseMapper $batchMapper
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->batchRequestFactory = $batchRequestFactory;
        $this->batchMapper = $batchMapper;
    }

    /**
     * @param string $batchId
     * @return BatchInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getById($batchId)
    {
        if (!$batchId) {
            throw new LocalizedException(__('An error occurred while loading data.'));
        }

        try {
            $request = $this->batchRequestFactory->create(['entityId' => $batchId]);
            $apiBatch = $this->apiAdapter->getBatch($request);
            $batch = $this->batchMapper->map($apiBatch);
        } catch (AdapterException $e) {
            if ($e->getCode() === 404) {
                throw NoSuchEntityException::singleField('batchId', $batchId);
            }

            throw new LocalizedException(__('An error occurred while loading data.'), $e);
        }

        return $batch;
    }
}
