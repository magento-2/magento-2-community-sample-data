<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;
use Temando\Shipping\Model\OrderInterfaceBuilder;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Manifest placed order at Temando platform
 *
 * @package  Temando\Shipping\Observer
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ManifestOrderObserver implements ObserverInterface
{
    /**
     * @var OrderReferenceInterfaceFactory
     */
    private $orderReferenceFactory;

    /**
     * @var OrderInterfaceBuilder
     */
    private $orderBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ManifestOrderObserver constructor.
     * @param OrderReferenceInterfaceFactory $orderReferenceFactory
     * @param OrderInterfaceBuilder $orderBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderReferenceInterfaceFactory $orderReferenceFactory,
        OrderInterfaceBuilder $orderBuilder,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->orderReferenceFactory = $orderReferenceFactory;
        $this->orderBuilder = $orderBuilder;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * Manifest order at Temando platform.
     *
     * Observer method must run on `sales_order_save_after`. It's the only
     * event that is reliably triggered
     * - in all checkout types
     * - after saving the order (so that order entity id is available)
     *
     * Other promising events like `sales_order_place_after`, `checkout_submit_all_after`,
     * `sales_model_service_quote_submit_success` are
     * - triggered before the order was saved or
     * - not triggered at all in multi address checkout or some payment provider's
     *   custom checkout implementations (paypal express, sagepay, …).
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $salesOrder */
        $salesOrder = $observer->getData('order');

        if (!$salesOrder->getData('shipping_method')) {
            // incomplete or wrong shipments can not be updated
            return;
        }

        $shippingMethod = $salesOrder->getShippingMethod(true);
        $carrierCode = $shippingMethod->getData('carrier_code');

        if ($carrierCode !== Carrier::CODE) {
            // not interested in other carriers
            return;
        }

        if ($salesOrder->hasShipments()) {
            // shipped orders cannot be updated at Temando platform
            return;
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
            return;
        }

        try {
            // create remote order entity from local (sales) order entity
            /** @var \Temando\Shipping\Model\OrderInterface $order */
            $this->orderBuilder->setOrder($salesOrder);
            $order = $this->orderBuilder->create();

            // save order at Temando platform as well as local reference to it.
            $this->orderRepository->save($order, $orderReference);
        } catch (\Exception $e) {
            // nothing we can do here, just don't interrupt order process
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
        }
    }
}
