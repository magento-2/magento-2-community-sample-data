<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Response\Type;

/**
 * Temando Order Save Result Interface
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface OrderResponseTypeInterface
{
    const EXT_ORDER_ID = 'ext_order_id';
    const SHIPPING_EXPERIENCES = 'shipping_experiences';
    const ERRORS = 'errors';
    const SHIPMENTS = 'shipments';
    const COLLECTION_POINTS = 'collection_points';

    /**
     * @return string
     */
    public function getExtOrderId();

    /**
     * @return \Temando\Shipping\Api\Data\Order\ShippingExperienceInterface[]
     */
    public function getShippingExperiences();

    /**
     * @return \Temando\Shipping\Model\Shipment\AllocationErrorInterface[]
     */
    public function getErrors();

    /**
     * @return \Temando\Shipping\Model\ShipmentInterface[]
     */
    public function getShipments();

    /**
     * @return \Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface[]
     */
    public function getCollectionPoints();
}
