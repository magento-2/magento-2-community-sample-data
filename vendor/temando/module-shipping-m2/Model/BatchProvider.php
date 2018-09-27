<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Temando Batch Provider
 *
 * Registry for re-use of the same batch entity during one request cycle.
 *
 * @package  Temando\Shipping\Model
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class BatchProvider implements BatchProviderInterface
{
    /**
     * @var BatchInterface
     */
    private $batch;

    /**
     * @var OrderInterface[]
     */
    private $orders = [];

    /**
     * @return BatchInterface
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param BatchInterface $batch
     * @return void
     */
    public function setBatch(BatchInterface $batch)
    {
        $this->batch = $batch;
    }

    /**
     * @return OrderInterface[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param OrderInterface[] $orders
     * @return void
     */
    public function setOrders(array $orders)
    {
        $this->orders = $orders;
    }
}
