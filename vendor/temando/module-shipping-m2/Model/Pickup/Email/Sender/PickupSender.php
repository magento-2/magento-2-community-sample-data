<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Pickup\Email\Sender;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Temando\Shipping\Model\Pickup\Email\Container\PickupIdentity;

/**
 * Temando Pickup Sender
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupSender extends OrderSender
{
    /**
     * @param string $path
     * @return void
     */
    private function setTemplatePath(string $path): void
    {
        if ($this->identityContainer instanceof PickupIdentity) {
            $this->identityContainer->setTemplatePath($path);
        }
    }

    /**
     * @return void
     */
    public function setPickupReady(): void
    {
        $this->setTemplatePath('sales_email/temando_pickup/ready_template');
    }

    /**
     * @return void
     */
    public function setPickupCollected(): void
    {
        $this->setTemplatePath('sales_email/temando_pickup/collected_template');
    }

    /**
     * @return void
     */
    public function setPickupCancelled(): void
    {
        $this->setTemplatePath('sales_email/temando_pickup/canceled_template');
    }
}
