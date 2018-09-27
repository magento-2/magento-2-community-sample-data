<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Dispatch;

/**
 * Temando Dispatch Shipment Interface.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ShipmentInterface
{
    const SHIPMENT_ID = 'shipment_id';
    const STATUS = 'status';
    const MESSAGE = 'message';
    const ERRORS = 'errors';

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
    public function getMessage();

    /**
     * @return ErrorInterface
     */
    public function getErrors();
}
