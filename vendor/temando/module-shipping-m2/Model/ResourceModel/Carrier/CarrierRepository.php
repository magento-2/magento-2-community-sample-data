<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Carrier;

use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Model\CarrierInterface;
use Temando\Shipping\Model\ResourceModel\Repository\CarrierRepositoryInterface;
use Temando\Shipping\Rest\Adapter\CarrierApiInterface;
use Temando\Shipping\Rest\EntityMapper\CarrierResponseMapper;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\ListRequestInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\CarrierConfiguration;
use Temando\Shipping\Rest\Response\DataObject\CarrierIntegration;
use Temando\Shipping\Webservice\Filter\CollectionFilterFactory;
use Temando\Shipping\Webservice\Pagination\PaginationFactory;

/**
 * Temando Carrier Repository
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CarrierRepository implements CarrierRepositoryInterface
{
    /**
     * @var CarrierApiInterface
     */
    private $apiAdapter;

    /**
     * @var CollectionFilterFactory
     */
    private $filterFactory;

    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    /**
     * @var ListRequestInterfaceFactory
     */
    private $requestFactory;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $itemRequestFactory;

    /**
     * @var CarrierResponseMapper
     */
    private $carrierMapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CarrierRepository constructor.
     *
     * @param CarrierApiInterface $apiAdapter
     * @param CollectionFilterFactory $filterFactory
     * @param PaginationFactory $paginationFactory
     * @param ListRequestInterfaceFactory $requestFactory
     * @param ItemRequestInterfaceFactory $itemRequestFactory
     * @param CarrierResponseMapper $carrierMapper
     * @param LoggerInterface $logger
     */
    public function __construct(
        CarrierApiInterface $apiAdapter,
        CollectionFilterFactory $filterFactory,
        PaginationFactory $paginationFactory,
        ListRequestInterfaceFactory $requestFactory,
        ItemRequestInterfaceFactory $itemRequestFactory,
        CarrierResponseMapper $carrierMapper,
        LoggerInterface $logger
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->filterFactory = $filterFactory;
        $this->paginationFactory = $paginationFactory;
        $this->requestFactory = $requestFactory;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->carrierMapper = $carrierMapper;
        $this->logger = $logger;
    }

    /**
     * @param null $offset
     * @param null $limit
     * @return CarrierIntegration[]
     */
    private function getIntegrations($offset = null, $limit = null)
    {
        $pagination = $this->paginationFactory->create([
            'offset' => $offset,
            'limit' => $limit,
        ]);
        $filter = $this->filterFactory->create([
            'filters' => ['registered' => 'true'],
        ]);

        $request = $this->requestFactory->create([
            'pagination' => $pagination,
            'filter' => $filter,
        ]);

        return $this->apiAdapter->getCarrierIntegrations($request);
    }

    /**
     * @param null $offset
     * @param null $limit
     * @return CarrierConfiguration[]
     */
    private function getConfigurations($offset = null, $limit = null)
    {
        $pagination = $this->paginationFactory->create([
            'offset' => $offset,
            'limit' => $limit,
        ]);

        $request = $this->requestFactory->create([
            'pagination' => $pagination,
        ]);

        return $this->apiAdapter->getCarrierConfigurations($request);
    }

    /**
     * @param int|null $offset
     * @param int|null $limit
     * @return CarrierInterface[]
     */
    public function getList($offset = null, $limit = null)
    {
        try {
            $apiIntegrations = $this->getIntegrations($offset, $limit);
            $integrations = [];
            foreach ($apiIntegrations as $apiCarrierIntegration) {
                $integrations[$apiCarrierIntegration->getId()] = $apiCarrierIntegration;
            }

            $apiConfigurations = $this->getConfigurations($offset, $limit);
            $carriers = array_map(function (CarrierConfiguration $apiConfiguration) use ($integrations) {
                $integrationId = $apiConfiguration->getAttributes()->getIntegrationId();
                $integration = isset($integrations[$integrationId]) ? $integrations[$integrationId] : null;
                return $this->carrierMapper->map($apiConfiguration, $integration);
            }, $apiConfigurations);
        } catch (AdapterException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $carriers = [];
        }

        return $carriers;
    }

    /**
     * @param string $carrierConfigurationId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete($carrierConfigurationId)
    {
        try {
            $request = $this->itemRequestFactory->create(['entityId' => $carrierConfigurationId]);
            $this->apiAdapter->deleteCarrierConfiguration($request);
        } catch (AdapterException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            throw new CouldNotDeleteException(__('Unable to delete carrier.'), $e);
        }
    }
}
