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
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ShipmentInterface
{
    const SHIPMENT_ID = 'shipment_id';
    const ORDER_ID = 'order_id';
    const ORIGIN_ID = 'origin_id';
    const ORIGIN_LOCATION = 'origin_location';
    const DESTINATION_LOCATION = 'destination_location';
    const FULFILLMENT = 'fulfill';
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
     * @return \Temando\Shipping\Model\Shipment\ShipmentOriginInterface
     */
    public function getOriginLocation();

    /**
     * @return \Temando\Shipping\Model\Shipment\ShipmentDestinationInterface
     */
    public function getDestinationLocation();

    /**
     * @return \Temando\Shipping\Model\Shipment\FulfillmentInterface
     */
    public function getFulfillment();

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
