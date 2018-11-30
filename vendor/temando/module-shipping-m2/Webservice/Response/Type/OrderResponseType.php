<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Response\Type;

use Magento\Framework\DataObject;

/**
 * Temando Order Save Result
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderResponseType extends DataObject implements OrderResponseTypeInterface
{
    /**
     * @return string
     */
    public function getExtOrderId()
    {
        return $this->getData(self::EXT_ORDER_ID);
    }

    /**
     * @return \Temando\Shipping\Api\Data\Order\ShippingExperienceInterface[]
     */
    public function getShippingExperiences()
    {
        return $this->getData(self::SHIPPING_EXPERIENCES);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\AllocationErrorInterface[]
     */
    public function getErrors()
    {
        return $this->getData(self::ERRORS);
    }

    /**
     * @return \Temando\Shipping\Model\ShipmentInterface[]
     */
    public function getShipments()
    {
        return $this->getData(self::SHIPMENTS);
    }

    /**
     * @return \Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface[]
     */
    public function getCollectionPoints()
    {
        return $this->getData(self::COLLECTION_POINTS);
    }
}
