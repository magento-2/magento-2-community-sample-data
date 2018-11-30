<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Order;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterfaceFactory;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Rest\Adapter\OrderApiInterface;
use Temando\Shipping\Rest\EntityMapper\OrderRequestTypeBuilder;
use Temando\Shipping\Rest\EntityMapper\OrderResponseMapper;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\OrderRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\Type\OrderRequestTypeInterface;
use Temando\Shipping\Webservice\OrderActionLocator;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Order Repository
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @var OrderApiInterface
     */
    private $apiAdapter;

    /**
     * @var OrderRequestInterfaceFactory
     */
    private $requestFactory;

    /**
     * @var OrderRequestTypeBuilder
     */
    private $requestBuilder;

    /**
     * @var OrderResponseMapper
     */
    private $orderResponseMapper;

    /**
     * @var OrderReference
     */
    private $resource;

    /**
     * @var OrderReferenceInterfaceFactory
     */
    private $orderReferenceFactory;

    /**
     * @var OrderActionLocator
     */
    private $orderActionLocator;

    /**
     * OrderRepository constructor.
     * @param OrderApiInterface $apiAdapter
     * @param OrderRequestInterfaceFactory $requestFactory
     * @param OrderRequestTypeBuilder $requestBuilder
     * @param OrderResponseMapper $orderResponseMapper
     * @param OrderReference $resource
     * @param OrderReferenceInterfaceFactory $orderReferenceFactory
     * @param OrderActionLocator $orderActionLocator
     */
    public function __construct(
        OrderApiInterface $apiAdapter,
        OrderRequestInterfaceFactory $requestFactory,
        OrderRequestTypeBuilder $requestBuilder,
        OrderResponseMapper $orderResponseMapper,
        OrderReference $resource,
        OrderReferenceInterfaceFactory $orderReferenceFactory,
        OrderActionLocator $orderActionLocator
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->requestFactory = $requestFactory;
        $this->requestBuilder = $requestBuilder;
        $this->orderResponseMapper = $orderResponseMapper;
        $this->resource = $resource;
        $this->orderReferenceFactory = $orderReferenceFactory;
        $this->orderActionLocator = $orderActionLocator;
    }

    /**
     * @param int $entityId
     * @return OrderReferenceInterface
     * @throws NoSuchEntityException
     */
    private function getReferenceById($entityId)
    {
        /** @var \Temando\Shipping\Model\Order\OrderReference $orderReference */
        $orderReference = $this->orderReferenceFactory->create();
        $this->resource->load($orderReference, $entityId);

        if (!$orderReference->getId()) {
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $entityId));
        }

        return $orderReference;
    }

    /**
     * @param OrderRequestTypeInterface $orderType
     * @return OrderResponseTypeInterface
     * @throws CouldNotSaveException
     */
    private function create(OrderRequestTypeInterface $orderType)
    {
        $orderRequest = $this->requestFactory->create([
            'order' => $orderType,
        ]);

        try {
            $createdOrder = $this->apiAdapter->createOrder($orderRequest);
        } catch (AdapterException $e) {
            throw new CouldNotSaveException(__('Unable to save order.'), $e);
        }

        return $this->orderResponseMapper->mapCreatedOrder($createdOrder);
    }

    /**
     * @param OrderRequestTypeInterface $orderType
     * @return OrderResponseTypeInterface
     * @throws CouldNotSaveException
     */
    private function quoteCollectionPoints(OrderRequestTypeInterface $orderType)
    {
        $orderRequest = $this->requestFactory->create([
            'order' => $orderType,
        ]);

        try {
            $quotedOrder = $this->apiAdapter->getCollectionPoints($orderRequest);
        } catch (AdapterException $e) {
            throw new CouldNotSaveException(__('Unable to get quotes.'), $e);
        }

        return $this->orderResponseMapper->mapCollectionPoints($quotedOrder);
    }

    /**
     * @param OrderRequestTypeInterface $orderType
     * @return OrderResponseTypeInterface
     * @throws CouldNotSaveException
     */
    private function allocate(OrderRequestTypeInterface $orderType)
    {
        $orderRequest = $this->requestFactory->create([
            'order' => $orderType,
        ]);

        try {
            $allocatedOrder = $this->apiAdapter->allocateOrder($orderRequest);
        } catch (AdapterException $e) {
            throw new CouldNotSaveException(__('Unable to allocate shipments.'), $e);
        }

        return $this->orderResponseMapper->mapAllocatedOrder($allocatedOrder);
    }

    /**
     * @param OrderRequestTypeInterface $orderType
     * @return OrderResponseTypeInterface
     * @throws CouldNotSaveException
     */
    private function update(OrderRequestTypeInterface $orderType)
    {
        $orderRequest = $this->requestFactory->create([
            'order' => $orderType,
            'orderId' => $orderType->getId(),
        ]);

        try {
            $updatedOrder = $this->apiAdapter->updateOrder($orderRequest);
        } catch (AdapterException $e) {
            throw new CouldNotSaveException(__('Unable to save order.'), $e);
        }

        return $this->orderResponseMapper->mapUpdatedOrder($updatedOrder);
    }

    /**
     * @param OrderInterface $order
     * @return OrderResponseTypeInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderInterface $order)
    {
        // build order request type
        $orderType = $this->requestBuilder->build($order);

        $apiAction = $this->orderActionLocator->getOrderAction($order);
        switch ($apiAction) {
            case OrderActionLocator::ACTION_NONE:
                $orderResponse = $this->orderResponseMapper->createEmptyResponse();
                break;
            case OrderActionLocator::ACTION_QUALIFY:
            case OrderActionLocator::ACTION_PERSIST:
                $orderResponse = $this->create($orderType);
                break;
            case OrderActionLocator::ACTION_ALLOCATE:
                $orderResponse = $this->allocate($orderType);
                break;
            case OrderActionLocator::ACTION_QUOTE_COLLECTION_POINTS:
                $orderResponse = $this->quoteCollectionPoints($orderType);
                break;
            case OrderActionLocator::ACTION_UPDATE:
                $orderResponse = $this->update($orderType);
                break;
            default:
                throw new CouldNotSaveException(__('Cannot save order: no applicable API action found.'));
        }

        if ($order->getSourceId() && !$order->getOrderId()) {
            // persist order reference if
            // - local order entity exists
            // - remote order entity does not yet exist
            $orderReference = $this->orderReferenceFactory->create(['data' => [
                OrderReferenceInterface::EXT_ORDER_ID => $orderResponse->getExtOrderId(),
                OrderReferenceInterface::ORDER_ID => $order->getSourceId(),
            ]]);

            $this->saveReference($orderReference);
        }

        return $orderResponse;
    }

    /**
     * @param OrderReferenceInterface $orderReference
     * @return OrderReferenceInterface
     * @throws CouldNotSaveException
     */
    public function saveReference(OrderReferenceInterface $orderReference)
    {
        try {
            /** @var \Temando\Shipping\Model\Order\OrderReference $orderReference */
            $this->resource->save($orderReference);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save order reference.'), $exception);
        }

        return $orderReference;
    }

    /**
     * @param string $orderId Temando Order ID
     * @return OrderReferenceInterface
     * @throws NoSuchEntityException
     */
    public function getReferenceByExtOrderId($orderId)
    {
        $entityId = $this->resource->getIdByExtOrderId($orderId);
        if (!$entityId) {
            throw new NoSuchEntityException(__('Order reference to "%1" does not exist.', $orderId));
        }

        return $this->getReferenceById($entityId);
    }

    /**
     * @param int $orderId
     * @return OrderReferenceInterface
     * @throws NoSuchEntityException
     */
    public function getReferenceByOrderId($orderId)
    {
        $entityId = $this->resource->getIdByOrderId($orderId);
        if (!$entityId) {
            $msg = 'Order reference for order "%1" does not exist.';
            throw new NoSuchEntityException(__($msg, $orderId));
        }

        return $this->getReferenceById($entityId);
    }
}
