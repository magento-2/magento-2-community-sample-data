<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

/**
 * Temando Batch Interface.
 *
 * The batch data object represents one item in the batches
 * grid listing or on the batch details page.
 *
 * @package  Temando\Shipping\Model
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface BatchInterface
{
    const BATCH_ID = 'batch_id';
    const STATUS = 'status';
    const CREATED_AT_DATE = 'created_at_date';
    const UPDATED_AT_DATE = 'updated_at_date';
    const INCLUDED_SHIPMENTS = 'included_shipments';
    const FAILED_SHIPMENTS = 'failed_shipments';
    const DOCUMENTATION = 'documentation';

    /**
     * @return string
     */
    public function getBatchId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getCreatedAtDate();

    /**
     * @return string
     */
    public function getUpdatedAtDate();

    /**
     * @return \Temando\Shipping\Model\Shipment\ShipmentSummaryInterface[]
     */
    public function getIncludedShipments();

    /**
     * @return \Temando\Shipping\Model\Shipment\ShipmentSummaryInterface[]
     */
    public function getFailedShipments();

    /**
     * @return \Temando\Shipping\Model\DocumentationInterface[]
     */
    public function getDocumentation();
}
