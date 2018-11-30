<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Sales;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface as SalesOrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\Model\Shipping\RateRequest\RequestDataInitializer;
use Temando\Shipping\Webservice\Processor\OrderOperationProcessorPool;

/**
 * OrderRepositoryPlugin
 *
 * @package Temando\Shipping\Plugin
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class OrderRepositoryPlugin
{
    /**
     * @var OrderReferenceInterfaceFactory
     */
    private $orderReferenceFactory;

    /**
     * @var RequestDataInitializer
     */
    private $requestDataInitializer;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderOperationProcessorPool
     */
    private $processorPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderRepositoryPlugin constructor.
     * @param OrderReferenceInterfaceFactory $orderReferenceFactory
     * @param RequestDataInitializer $orderDataInitializer
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderOperationProcessorPool $processorPool
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderReferenceInterfaceFactory $orderReferenceFactory,
        RequestDataInitializer $orderDataInitializer,
        OrderRepositoryInterface $orderRepository,
        OrderOperationProcessorPool $processorPool,
        LoggerInterface $logger
    ) {
        $this->orderReferenceFactory = $orderReferenceFactory;
        $this->requestDataInitializer = $orderDataInitializer;
        $this->orderRepository = $orderRepository;
        $this->processorPool = $processorPool;
        $this->logger = $logger;
    }

    /**
     * Manifest order at Temando platform.
     *
     * Observers don't work:
     * - `sales_order_save_commit_after` is no longer triggered in guest checkout
     * - `sales_order_save_after` does not have related entities (addresses) persisted yet
     *
     * Other promising events like `sales_order_place_after`, `checkout_submit_all_after`,
     * `sales_model_service_quote_submit_success` are
     * - triggered before the order was saved or
     * - not triggered at all in multi address checkout or some payment providers'
     *   custom checkout implementations (paypal express, sagepay, …).
     *
     * @param SalesOrderRepositoryInterface $subject
     * @param OrderInterface|\Magento\Sales\Model\Order $salesOrder
     * @return OrderInterface
     */
    public function afterSave(SalesOrderRepositoryInterface $subject, OrderInterface $salesOrder)
    {
        if (!$salesOrder->getData('shipping_method')) {
            // virtual or corrupt order
            return $salesOrder;
        }

        $shippingMethod = $salesOrder->getShippingMethod(true);
        $carrierCode = $shippingMethod->getData('carrier_code');

        if ($carrierCode !== Carrier::CODE) {
            // not interested in other carriers
            return $salesOrder;
        }

        try {
            $orderReference = $this->orderRepository->getReferenceByOrderId($salesOrder->getId());
        } catch (NoSuchEntityException $e) {
            $orderReference = $this->orderReferenceFactory->create(['data' => [
                OrderReferenceInterface::ORDER_ID => $salesOrder->getId(),
            ]]);
        }

        if ($orderReference->getExtOrderId()) {
            // Do not send orders to Temando platform that were saved already.
            return $salesOrder;
        }

        try {
            // create remote order entity from local (sales) order entity
            $order = $this->requestDataInitializer->getManifestationData($salesOrder);
            // save order at Temando platform as well as local reference to it.
            $saveResult = $this->orderRepository->save($order);

            $this->processorPool->processSaveResponse($salesOrder, $order, $saveResult);
        } catch (\Exception $e) {
            // nothing we can do here, just don't interrupt order process
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
        }

        return $salesOrder;
    }
}
