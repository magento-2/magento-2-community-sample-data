<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order\AutoProcessing;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Temando\Shipping\Model\Shipment\AllocationErrorInterface;

/**
 * Temando Order Fulfillment Comments History Updater.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderStatusHistoryUpdater
{
    /**
     * @var OrderStatusHistoryInterfaceFactory
     */
    private $historyFactory;

    /**
     * @var OrderStatusHistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * OrderStatusHistoryUpdater constructor.
     * @param OrderStatusHistoryInterfaceFactory $historyFactory
     * @param OrderStatusHistoryRepositoryInterface $historyRepository
     */
    public function __construct(
        OrderStatusHistoryInterfaceFactory $historyFactory,
        OrderStatusHistoryRepositoryInterface $historyRepository
    ) {
        $this->historyFactory = $historyFactory;
        $this->historyRepository = $historyRepository;
    }

    /**
     * @param OrderInterface $order
     * @param string $comment
     * @return bool
     */
    public function addComment(OrderInterface $order, $comment)
    {
        $historyItem = $this->historyFactory->create(['data' => [
            OrderStatusHistoryInterface::COMMENT => $comment,
            OrderStatusHistoryInterface::PARENT_ID => $order->getEntityId(),
            OrderStatusHistoryInterface::ENTITY_NAME => 'order',
            OrderStatusHistoryInterface::STATUS => $order->getStatus(),
            OrderStatusHistoryInterface::IS_CUSTOMER_NOTIFIED => false,
        ]]);

        try {
            $this->historyRepository->save($historyItem);
            return true;
        } catch (CouldNotSaveException $exception) {
            return false;
        }
    }

    /**
     * @param OrderInterface $order
     * @param AllocationErrorInterface[] $errors
     * @return void
     */
    public function addErrors(OrderInterface $order, array $errors)
    {
        foreach ($errors as $error) {
            $comment = $error->getDetail()
                ? sprintf('%s - %s', $error->getTitle(), $error->getDetail())
                : $error->getTitle();

            $this->addComment($order, $comment);
        }
    }
}
