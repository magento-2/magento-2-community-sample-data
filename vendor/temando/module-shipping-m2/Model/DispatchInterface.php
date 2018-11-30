<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

/**
 * Temando Dispatch Interface.
 *
 * The dispatch/completion data object represents one item in the dispatches
 * grid listing or on the dispatch details page.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface DispatchInterface
{
    const DISPATCH_ID = 'dispatch_id';
    const STATUS = 'status';
    const CARRIER_NAME = 'carrier_name';
    const CARRIER_MESSAGES = 'carrier_messages';
    const CREATED_AT_DATE = 'created_at_date';
    const READY_AT_DATE = 'ready_at_date';
    const PICKUP_NUMBERS = 'pickup_numbers';
    const PICKUP_CHARGES = 'pickup_charges';
    const INCLUDED_SHIPMENTS = 'included_shipments';
    const FAILED_SHIPMENTS = 'failed_shipments';
    const DOCUMENTATION = 'documentation';

    /**
     * @return string
     */
    public function getDispatchId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getCarrierName();

    /**
     * @return string[]
     */
    public function getCarrierMessages();

    /**
     * @return string
     */
    public function getCreatedAtDate();

    /**
     * @return string
     */
    public function getReadyAtDate();

    /**
     * @return string[]
     */
    public function getPickupNumbers();

    /**
     * @return \Temando\Shipping\Model\Dispatch\PickupChargeInterface[]
     */
    public function getPickupCharges();

    /**
     * @return \Temando\Shipping\Model\Dispatch\ShipmentInterface[]
     */
    public function getIncludedShipments();

    /**
     * @return \Temando\Shipping\Model\Dispatch\ShipmentInterface[]
     */
    public function getFailedShipments();

    /**
     * @return \Temando\Shipping\Model\DocumentationInterface[]
     */
    public function getDocumentation();
}
