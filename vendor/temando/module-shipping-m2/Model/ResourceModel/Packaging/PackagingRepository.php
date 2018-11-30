<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Packaging;

use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Model\PackagingInterface;
use Temando\Shipping\Model\ResourceModel\Repository\PackagingRepositoryInterface;
use Temando\Shipping\Rest\Adapter\ContainerApiInterface;
use Temando\Shipping\Rest\EntityMapper\PackagingResponseMapper;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\ListRequestInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\Container;
use Temando\Shipping\Webservice\Pagination\PaginationFactory;

/**
 * Temando Packaging Repository
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PackagingRepository implements PackagingRepositoryInterface
{
    /**
     * @var ContainerApiInterface
     */
    private $apiAdapter;

    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    /**
     * @var ListRequestInterfaceFactory
     */
    private $listRequestFactory;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $itemRequestFactory;

    /**
     * @var PackagingResponseMapper
     */
    private $packagingMapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PackagingRepository constructor.
     * @param ContainerApiInterface $apiAdapter
     * @param PaginationFactory $paginationFactory
     * @param ListRequestInterfaceFactory $listRequestFactory
     * @param ItemRequestInterfaceFactory $itemRequestFactory
     * @param PackagingResponseMapper $packagingMapper
     * @param LoggerInterface $logger
     */
    public function __construct(
        ContainerApiInterface $apiAdapter,
        PaginationFactory $paginationFactory,
        ListRequestInterfaceFactory $listRequestFactory,
        ItemRequestInterfaceFactory $itemRequestFactory,
        PackagingResponseMapper $packagingMapper,
        LoggerInterface $logger
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->paginationFactory = $paginationFactory;
        $this->listRequestFactory = $listRequestFactory;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->packagingMapper = $packagingMapper;
        $this->logger = $logger;
    }

    /**
     * @param int|null $offset
     * @param int|null $limit
     * @return PackagingInterface[]
     */
    public function getList($offset = null, $limit = null)
    {
        try {
            $pagination = $this->paginationFactory->create([
                'offset' => $offset,
                'limit' => $limit,
            ]);

            $request = $this->listRequestFactory->create([
                'pagination' => $pagination,
            ]);

            $apiContainers = $this->apiAdapter->getContainers($request);
            $containers = array_map(function (Container $apiContainer) {
                return $this->packagingMapper->map($apiContainer);
            }, $apiContainers);
        } catch (AdapterException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $containers = [];
        }

        return $containers;
    }

    /**
     * @param string $packagingId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete($packagingId)
    {
        try {
            $request = $this->itemRequestFactory->create(['entityId' => $packagingId]);
            $this->apiAdapter->deleteContainer($request);
        } catch (AdapterException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            throw new CouldNotDeleteException(__('Unable to delete packaging.'), $e);
        }
    }
}
