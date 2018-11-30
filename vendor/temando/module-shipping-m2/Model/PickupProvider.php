<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Temando Pickup Provider.
 *
 * Registers pickup and related entities for the current request cycle
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupProvider implements PickupProviderInterface
{
    /**
     * @var PickupInterface
     */
    private $pickup;

    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var PickupInterface[]
     */
    private $pickups = [];

    /**
     * @return PickupInterface|null
     */
    public function getPickup()
    {
        return $this->pickup;
    }

    /**
     * @param PickupInterface $pickup
     *
     * @return void
     */
    public function setPickup(PickupInterface $pickup)
    {
        $this->pickup = $pickup;
    }

    /**
     * @return OrderInterface|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param OrderInterface $order
     *
     * @return void
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @return PickupInterface[]
     */
    public function getPickups()
    {
        return $this->pickups;
    }

    /**
     * @param PickupInterface[] $pickups
     *
     * @return void
     */
    public function setPickups(array $pickups)
    {
        $this->pickups = $pickups;
    }
}
