<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Pickup;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\Model\ResourceModel\Order\OrderReference;
use Temando\Shipping\Model\ResourceModel\Repository\PickupRepositoryInterface;
use Temando\Shipping\Rest\Adapter\FulfillmentApiInterface;
use Temando\Shipping\Rest\EntityMapper\FulfillmentResponseMapper;
use Temando\Shipping\Rest\Filter\FilterConverter;
use Temando\Shipping\Rest\Pagination\PaginationFactory;
use Temando\Shipping\Rest\Request\FulfillmentRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\ListRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\Type\FulfillmentRequestTypeInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\Fulfillment;

/**
 * Temando Pickup Fulfillment Repository
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupRepository implements PickupRepositoryInterface
{
    /**
     * @var FulfillmentApiInterface
     */
    private $apiAdapter;

    /**
     * @var FilterConverter
     */
    private $filterConverter;

    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    /**
     * @var OrderReference
     */
    private $orderReferenceResource;

    /**
     * @var ListRequestInterfaceFactory
     */
    private $listRequestFactory;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $itemRequestFactory;

    /**
     * @var FulfillmentRequestInterfaceFactory
     */
    private $fulfillmentRequestFactory;

    /**
     * @var FulfillmentRequestTypeInterfaceFactory
     */
    private $requestTypeFactory;

    /**
     * @var FulfillmentResponseMapper
     */
    private $fulfillmentMapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PickupRepository constructor.
     * @param FulfillmentApiInterface $apiAdapter
     * @param FilterConverter $filterConverter
     * @param PaginationFactory $paginationFactory
     * @param OrderReference $orderReferenceResource
     * @param ListRequestInterfaceFactory $listRequestFactory
     * @param ItemRequestInterfaceFactory $itemRequestFactory
     * @param FulfillmentRequestInterfaceFactory $fulfillmentRequestFactory
     * @param FulfillmentRequestTypeInterfaceFactory $requestTypeFactory
     * @param FulfillmentResponseMapper $fulfillmentMapper
     * @param LoggerInterface $logger
     */
    public function __construct(
        FulfillmentApiInterface $apiAdapter,
        FilterConverter $filterConverter,
        PaginationFactory $paginationFactory,
        OrderReference $orderReferenceResource,
        ListRequestInterfaceFactory $listRequestFactory,
        ItemRequestInterfaceFactory $itemRequestFactory,
        FulfillmentRequestInterfaceFactory $fulfillmentRequestFactory,
        FulfillmentRequestTypeInterfaceFactory $requestTypeFactory,
        FulfillmentResponseMapper $fulfillmentMapper,
        LoggerInterface $logger
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->filterConverter = $filterConverter;
        $this->paginationFactory = $paginationFactory;
        $this->orderReferenceResource = $orderReferenceResource;
        $this->listRequestFactory = $listRequestFactory;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->fulfillmentRequestFactory = $fulfillmentRequestFactory;
        $this->requestTypeFactory = $requestTypeFactory;
        $this->fulfillmentMapper = $fulfillmentMapper;
        $this->logger = $logger;
    }

    /**
     * Filter callbacks allow to change values prior to sending them to the platform.
     *
     * @return \Closure[]
     */
    private function getFilterCallbacks()
    {
        // convert local order id to platform order id
        $orderFilterCallback = function ($salesOrderId) {
            return $this->orderReferenceResource->getExtOrderIdByOrderId($salesOrderId);
        };

        return [
            PickupInterface::ORDER_ID => $orderFilterCallback,
        ];
    }

    /**
     * @param string $pickupId
     * @return PickupInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getById($pickupId)
    {
        if (!$pickupId) {
            throw new LocalizedException(__('An error occurred while loading data.'));
        }

        try {
            $request = $this->itemRequestFactory->create(['entityId' => $pickupId]);
            $apiFulfillment = $this->apiAdapter->getFulfillment($request);
            $pickup = $this->fulfillmentMapper->mapPickup($apiFulfillment);
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                throw NoSuchEntityException::singleField('fulfillmentId', $pickupId);
            }

            throw new LocalizedException(__('An error occurred while loading data.'), $e);
        }

        return $pickup;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @return PickupInterface[]
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        try {
            $pagination = $this->paginationFactory->create([
                'offset' => 0,
                'limit' => 1000,
            ]);
            $filter = $this->filterConverter->convert(
                $criteria->getFilterGroups(),
                $this->getFilterCallbacks()
            );

            $request = $this->listRequestFactory->create([
                'pagination' => $pagination,
                'filter' => $filter,
            ]);

            $apiFulfillments = $this->apiAdapter->getFulfillments($request);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            $apiFulfillments = [];
        }

        $pickups = array_map(function (Fulfillment $apiFulfillment) {
            return $this->fulfillmentMapper->mapPickup($apiFulfillment);
        }, $apiFulfillments);

        return $pickups;
    }

    /**
     * @param PickupInterface $pickup
     * @return PickupInterface
     * @throws CouldNotSaveException
     */
    public function save(PickupInterface $pickup)
    {
        // build fulfillment request type
        $fulfillmentType = $this->requestTypeFactory->create([
            'id' => $pickup->getPickupId(),
            'type' => 'fulfillment-pickup',
            'reference' => $pickup->getOrderReference(),
            'state' => $pickup->getState(),
            'orderId' => $pickup->getOrderId(),
            'pickupLocationId' => $pickup->getLocationId(),
            'items' => $pickup->getItems() ?: [],
        ]);

        $fulfillmentRequest = $this->fulfillmentRequestFactory->create([
            'fulfillment' => $fulfillmentType,
        ]);

        try {
            if ($pickup->getPickupId()) {
                $apiFulfillment = $this->apiAdapter->updateFulfillment($fulfillmentRequest);
            } else {
                $apiFulfillment = $this->apiAdapter->createFulfillment($fulfillmentRequest);
            }

            $fulfillment = $this->fulfillmentMapper->mapPickup($apiFulfillment);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save pickup.'), $e);
        }

        return $fulfillment;
    }
}
