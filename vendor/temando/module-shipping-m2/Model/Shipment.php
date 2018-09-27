<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Framework\DataObject;

/**
 * Temando Shipment Entity
 *
 * This model contains a subset of data that is used in the shipping module.
 * It does not contain all data as available in its platform representation.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Shipment extends DataObject implements ShipmentInterface
{
    /**
     * @return string
     */
    public function getShipmentId()
    {
        return $this->getData(ShipmentInterface::SHIPMENT_ID);
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->getData(ShipmentInterface::ORDER_ID);
    }

    /**
     * @return string
     */
    public function getOriginId()
    {
        return $this->getData(ShipmentInterface::ORIGIN_ID);
    }

    /**
     * @return string
     */
    public function getCustomerReference()
    {
        return $this->getData(ShipmentInterface::CUSTOMER_REFERENCE);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\Location
     */
    public function getOriginLocation()
    {
        return $this->getData(ShipmentInterface::ORIGIN_LOCATION);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\Location
     */
    public function getDestinationLocation()
    {
        return $this->getData(ShipmentInterface::DESTINATION_LOCATION);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\Location
     */
    public function getFinalRecipientLocation()
    {
        return $this->getData(ShipmentInterface::FINAL_RECIPIENT_LOCATION);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\FulfillmentInterface
     */
    public function getFulfillment()
    {
        return $this->getData(ShipmentInterface::FULFILLMENT);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\ShipmentItemInterface[]
     */
    public function getItems()
    {
        return $this->getData(ShipmentInterface::ITEMS);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\PackageInterface[]
     */
    public function getPackages()
    {
        return $this->getData(ShipmentInterface::PACKAGES);
    }

    /**
     * @return DocumentationInterface[]
     */
    public function getDocumentation()
    {
        return $this->getData(ShipmentInterface::DOCUMENTATION);
    }

    /**
     * @return bool
     */
    public function isPaperless()
    {
        return $this->getData(ShipmentInterface::IS_PAPERLESS);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\ExportDeclarationInterface
     */
    public function getExportDeclaration()
    {
        return $this->getData(ShipmentInterface::EXPORT_DECLARATION);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(ShipmentInterface::STATUS);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\CapabilityInterface[]
     */
    public function getCapabilities()
    {
        return $this->getData(ShipmentInterface::CAPABILITIES);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(ShipmentInterface::CREATED_AT);
    }
}
