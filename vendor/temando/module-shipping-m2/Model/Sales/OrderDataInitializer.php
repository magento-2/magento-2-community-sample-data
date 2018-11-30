<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Sales;

use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\OrderInterfaceBuilder;

/**
 * Temando Order Data Initializer.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderDataInitializer
{
    /**
     * @var OrderInterfaceBuilder
     */
    private $orderBuilder;

    /**
     * OrderDataInitializer constructor.
     * @param OrderInterfaceBuilder $orderBuilder
     */
    public function __construct(OrderInterfaceBuilder $orderBuilder)
    {
        $this->orderBuilder = $orderBuilder;
    }

    /**
     * Create a Temando order for update purposes after the order was placed.
     *
     * The order is being built from the placed order and may include dynamic checkout fields,
     *
     * NOTE: Delivery locations will currently not be considered when updating an order.
     *
     * @param OrderInterface $order
     * @return \Temando\Shipping\Model\OrderInterface
     */
    public function getOrder(OrderInterface $order)
    {
        $this->orderBuilder->setOrder($order);

        /** @var \Temando\Shipping\Model\OrderInterface $order */
        $order = $this->orderBuilder->create();

        return $order;
    }
}
