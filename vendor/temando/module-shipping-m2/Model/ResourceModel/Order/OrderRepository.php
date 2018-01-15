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
use Temando\Shipping\Rest\Response\UpdateOrder;

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
    private $orderReferenceMapper;

    /**
     * @var OrderReference
     */
    private $resource;

    /**
     * @var OrderReferenceInterfaceFactory
     */
    private $orderReferenceFactory;

    /**
     * OrderRepository constructor.
     * @param OrderApiInterface $apiAdapter
     * @param OrderRequestInterfaceFactory $requestFactory
     * @param OrderRequestTypeBuilder $requestBuilder
     * @param OrderResponseMapper $orderReferenceMapper
     * @param OrderReference $resource
     * @param OrderReferenceInterfaceFactory $orderReferenceFactory
     */
    public function __construct(
        OrderApiInterface $apiAdapter,
        OrderRequestInterfaceFactory $requestFactory,
        OrderRequestTypeBuilder $requestBuilder,
        OrderResponseMapper $orderReferenceMapper,
        OrderReference $resource,
        OrderReferenceInterfaceFactory $orderReferenceFactory
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->requestFactory = $requestFactory;
        $this->requestBuilder = $requestBuilder;
        $this->orderReferenceMapper = $orderReferenceMapper;
        $this->resource = $resource;
        $this->orderReferenceFactory = $orderReferenceFactory;
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
     * @return UpdateOrder
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
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        }

        return $createdOrder;
    }

    /**
     * @param OrderRequestTypeInterface $orderType
     * @return UpdateOrder
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
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        }

        return $updatedOrder;
    }

    /**
     * @param OrderInterface $order
     * @param OrderReferenceInterface $orderReference
     * @return OrderReferenceInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderInterface $order, OrderReferenceInterface $orderReference)
    {
        // local extension attribute identifier
        $orderReferenceId = $orderReference->getEntityId();
        // remote order entity identifier
        $platformOrderId = $orderReference->getExtOrderId();
        // local order entity identifier
        $salesOrderId = $orderReference->getOrderId();

        // build order request type
        $orderType = $this->requestBuilder->build($order);

        if (!$platformOrderId) {
            $orderResponse = $this->create($orderType);
        } else {
            $orderResponse = $this->update($orderType);
        }

        // create or update local order reference
        $orderReference = $this->orderReferenceMapper->map($orderResponse);
        $orderReference->setEntityId($orderReferenceId);
        $orderReference->setOrderId($salesOrderId);

        if (!$orderReference->getOrderId()) {
            // no local reference key available yet
            return $orderReference;
        }

        return $this->saveReference($orderReference);
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
            throw new CouldNotSaveException(__($exception->getMessage()));
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
