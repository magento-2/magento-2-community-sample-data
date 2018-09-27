<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

/**
 * Temando Shipment Interface.
 *
 * The shipment data object represents one shipment as created at the Temando
 * platform. It contains only a subset of shipping attributes that might be
 * available at the API.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface ShipmentInterface
{
    const SHIPMENT_ID = 'shipment_id';
    const ORDER_ID = 'order_id';
    const ORIGIN_ID = 'origin_id';
    const CUSTOMER_REFERENCE = 'customer_reference';
    const ORIGIN_LOCATION = 'origin_location';
    const DESTINATION_LOCATION = 'destination_location';
    const FINAL_RECIPIENT_LOCATION = 'final_recipient_location';
    const FULFILLMENT = 'fulfill';
    const ITEMS = 'items';
    const PACKAGES = 'packages';
    const DOCUMENTATION = 'documentation';
    const IS_PAPERLESS = 'is_paperless';
    const EXPORT_DECLARATION = 'export_declaration';
    const STATUS = 'status';
    const CAPABILITIES = 'capabilities';
    const CREATED_AT = 'created_at';

    /**
     * @return string
     */
    public function getShipmentId();

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getOriginId();

    /**
     * @return string
     */
    public function getCustomerReference();

    /**
     * @return \Temando\Shipping\Model\Shipment\Location
     */
    public function getOriginLocation();

    /**
     * @return \Temando\Shipping\Model\Shipment\Location
     */
    public function getDestinationLocation();

    /**
     * @return \Temando\Shipping\Model\Shipment\Location
     */
    public function getFinalRecipientLocation();

    /**
     * @return \Temando\Shipping\Model\Shipment\FulfillmentInterface
     */
    public function getFulfillment();

    /**
     * @return \Temando\Shipping\Model\Shipment\ShipmentItemInterface[]
     */
    public function getItems();

    /**
     * @return \Temando\Shipping\Model\Shipment\PackageInterface[]
     */
    public function getPackages();

    /**
     * @return \Temando\Shipping\Model\DocumentationInterface[]
     */
    public function getDocumentation();

    /**
     * @return bool
     */
    public function isPaperless();

    /**
     * @return \Temando\Shipping\Model\Shipment\ExportDeclarationInterface
     */
    public function getExportDeclaration();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return \Temando\Shipping\Model\Shipment\CapabilityInterface[]
     */
    public function getCapabilities();

    /**
     * @return string
     */
    public function getCreatedAt();
}
