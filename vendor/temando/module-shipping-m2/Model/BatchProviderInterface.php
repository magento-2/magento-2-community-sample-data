<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Temando Batch Provider
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface BatchProviderInterface
{
    /**
     * @return BatchInterface
     */
    public function getBatch();

    /**
     * @param BatchInterface $batch
     * @return void
     */
    public function setBatch(BatchInterface $batch);

    /**
     * @return OrderInterface[]
     */
    public function getOrders();

    /**
     * @param OrderInterface[] $orders
     * @return void
     */
    public function setOrders(array $orders);
}
