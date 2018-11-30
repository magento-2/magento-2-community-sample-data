<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Shipment Summary Interface.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface ShipmentSummaryInterface
{
    const ORDER_ID          = 'order_id';
    const SHIPMENT_ID       = 'shipment_id';
    const STATUS            = 'status';
    const RECIPIENT_ADDRESS = 'recipient_address';
    const RECIPIENT_NAME    = 'recipient_name';
    const ERRORS            = 'errors';

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getShipmentId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getRecipientAddress();

    /**
     * @return string
     */
    public function getRecipientName();

    /**
     * @return ShipmentErrorInterface[]
     */
    public function getErrors();
}
