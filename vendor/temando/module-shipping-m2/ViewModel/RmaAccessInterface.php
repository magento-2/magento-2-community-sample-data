<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel;

use Magento\Rma\Api\Data\RmaInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * RMA Details Provider Interface
 *
 * All view models that provide access to RMA details must implement this.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface RmaAccessInterface
{
    /**
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface;

    /**
     * @return RmaInterface
     */
    public function getRma(): RmaInterface;

    /**
     * @deprecated since 1.2.0 | no longer available
     * @return ShipmentInterface
     */
    public function getRmaShipment(): ShipmentInterface;
}
