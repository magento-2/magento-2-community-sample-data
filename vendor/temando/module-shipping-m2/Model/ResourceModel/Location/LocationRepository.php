<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\Location;

use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Model\LocationInterface;
use Temando\Shipping\Model\ResourceModel\Repository\LocationRepositoryInterface;
use Temando\Shipping\Rest\Adapter\LocationApiInterface;
use Temando\Shipping\Rest\EntityMapper\LocationResponseMapper;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\ListRequestInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\Location;
use Temando\Shipping\Webservice\Pagination\PaginationFactory;

/**
 * Temando Location Repository
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @var LocationApiInterface
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
     * @var LocationResponseMapper
     */
    private $locationMapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LocationRepository constructor.
     * @param LocationApiInterface $apiAdapter
     * @param PaginationFactory $paginationFactory
     * @param ListRequestInterfaceFactory $listRequestFactory
     * @param ItemRequestInterfaceFactory $itemRequestFactory
     * @param LocationResponseMapper $locationMapper
     * @param LoggerInterface $logger
     */
    public function __construct(
        LocationApiInterface $apiAdapter,
        PaginationFactory $paginationFactory,
        ListRequestInterfaceFactory $listRequestFactory,
        ItemRequestInterfaceFactory $itemRequestFactory,
        LocationResponseMapper $locationMapper,
        LoggerInterface $logger
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->paginationFactory = $paginationFactory;
        $this->listRequestFactory = $listRequestFactory;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->locationMapper = $locationMapper;
        $this->logger = $logger;
    }

    /**
     * @param int|null $offset
     * @param int|null $limit
     *
     * @return LocationInterface[]
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

            $apiLocations = $this->apiAdapter->getLocations($request);
            $locations = array_map(function (Location $apiLocation) {
                return $this->locationMapper->map($apiLocation);
            }, $apiLocations);
        } catch (AdapterException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $locations = [];
        }

        return $locations;
    }

    /**
     * @param string $locationId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete($locationId)
    {
        try {
            $request = $this->itemRequestFactory->create(['entityId' => $locationId]);
            $this->apiAdapter->deleteLocation($request);
        } catch (AdapterException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            throw new CouldNotDeleteException(__('Unable to delete location.'), $e);
        }
    }
}
