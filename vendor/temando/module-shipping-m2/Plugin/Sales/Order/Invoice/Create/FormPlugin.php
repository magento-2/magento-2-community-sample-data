<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Sales\Order\Invoice\Create;

use Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Form;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * FormPlugin
 *
 * @package  Temando\Shipping\Plugin
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class FormPlugin
{
    /**
     * Prevent shipment being created together with invoice as this would bypass
     * the OrderShip component and no shipping label would be created.
     *
     * @param Form $subject
     * @param bool $hasMismatch
     * @return bool
     */
    public function afterHasInvoiceShipmentTypeMismatch(Form $subject, $hasMismatch)
    {
        $order = $subject->getInvoice()->getOrder();
        $shippingMethod = $order->getShippingMethod();

        if (strpos($shippingMethod, Carrier::CODE) !== 0) {
            return $hasMismatch;
        }

        return true;
    }
}
