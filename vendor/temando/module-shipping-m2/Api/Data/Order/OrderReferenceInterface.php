<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\Order;

/**
 * Temando Order Reference Interface.
 *
 * An order reference represents the link between local quote/order address and
 * an order entity at the Temando platform. Creating an order at the Temando
 * platform results in a set of shipping experiences (shipping rates) applicable
 * to the given order data.
 *
 * @api
 * @package  Temando\Shipping\Api
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface OrderReferenceInterface
{
    const ENTITY_ID = 'entity_id';
    const ORDER_ID = 'order_id';
    const EXT_ORDER_ID = 'ext_order_id';
    const COLLECTION_POINT_ID = 'collection_point_id';
    const SHIPPING_EXPERIENCES = 'shipping_experiences';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return void
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return void
     */
    public function setOrderId($orderId);

    /**
     * @return string
     */
    public function getExtOrderId();

    /**
     * @param string $extOrderId
     * @return void
     */
    public function setExtOrderId($extOrderId);

    /**
     * @deprecated since 1.2.0 | never populated, never used.
     * @return \Temando\Shipping\Api\Data\Order\ShippingExperienceInterface[]
     */
    public function getShippingExperiences();

    /**
     * @deprecated since 1.2.0 | never populated, never used.
     * @param \Temando\Shipping\Api\Data\Order\ShippingExperienceInterface[] $shippingExperiences
     * @return void
     */
    public function setShippingExperiences(array $shippingExperiences);
}
