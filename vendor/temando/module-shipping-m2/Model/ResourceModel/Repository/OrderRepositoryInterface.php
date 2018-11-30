<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;

/**
 * Temando Order Repository Interface.
 *
 * An order entity is created at the Temando platform as soon as shipping rates
 * are requested from the API. A reference to the external order is stored
 * locally.
 *
 * This public interface can be used to create/update orders at the Temando
 * platform as well as creating/reading/updating the local reference.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface OrderRepositoryInterface
{
    /**
     * @param \Temando\Shipping\Model\OrderInterface $order
     * @return \Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderInterface $order);

    /**
     * @param \Temando\Shipping\Api\Data\Order\OrderReferenceInterface $orderReference
     * @return \Temando\Shipping\Api\Data\Order\OrderReferenceInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveReference(OrderReferenceInterface $orderReference);

    /**
     * @param string $orderId Temando Order ID
     * @return \Temando\Shipping\Api\Data\Order\OrderReferenceInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getReferenceByExtOrderId($orderId);

    /**
     * @param int $orderId
     * @return \Temando\Shipping\Api\Data\Order\OrderReferenceInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getReferenceByOrderId($orderId);
}
