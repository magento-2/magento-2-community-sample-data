<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order\AutoProcessing;

use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Order Fulfillment Processor Interface.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface AutoFulfillInterface
{
    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $salesOrder
     * @param \Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface $orderResponse
     * @return int[]
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createShipments(OrderInterface $salesOrder, OrderResponseTypeInterface $orderResponse);
}
