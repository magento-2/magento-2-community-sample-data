<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice;

use Temando\Shipping\Model\OrderInterface;

/**
 * Temando Order Action Locator
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderActionLocator
{
    /**
     * No API call
     */
    const ACTION_NONE = 'none';

    /**
     * Retrieve rates
     */
    const ACTION_QUALIFY = 'qualify';

    /**
     * Retrieve pickup locations with rates
     */
    const ACTION_QUALIFY_PICKUP = 'qualify_pickup';

    /**
     * Retrieve collection points with rates
     */
    const ACTION_QUALIFY_COLLECTION_POINTS = 'qualify_collection_points';

    /**
     * Manifest order
     */
    const ACTION_PERSIST = 'persist';

    /**
     * Manifest pickup order
     */
    const ACTION_PERSIST_PICKUP = 'persist_pickup';

    /**
     * Manifest order and allocate to shipment(s)
     */
    const ACTION_ALLOCATE = 'allocate';

    /**
     * Update order that was already persisted at platform
     */
    const ACTION_UPDATE = 'update';

    /**
     * Update placed order that was already created at the platform.
     *
     * @return string
     */
    private function getUpdateOrderAction()
    {
        return self::ACTION_UPDATE;
    }

    /**
     * Manifest new order at the platform, optionally with shipment allocation.
     *
     * @param OrderInterface $order
     * @return string
     */
    private function getCreateOrderAction(OrderInterface $order)
    {
        $orderStatus = $order->getStatus();

        $pickup = $order->getPickupLocation();
        $isPickupOrder = !empty($pickup) && !empty($pickup->getPickupLocationId());

        if ($isPickupOrder) {
            return self::ACTION_PERSIST_PICKUP;
        }

        // might be replaced by config setting in the future.
        $isOrderAllocationEnabled = true;

        if (!$isOrderAllocationEnabled) {
            // allocation disabled in config, regular persist action
            return self::ACTION_PERSIST;
        }

        if ($orderStatus === OrderInterface::STATUS_AWAITING_PAYMENT) {
            // order not ready for allocation, regular persist action
            return self::ACTION_PERSIST;
        }

        return self::ACTION_ALLOCATE;
    }

    /**
     * Obtain rates, optionally for collection points.
     *
     * @param OrderInterface $order
     * @return string
     */
    private function getQualifyOrderAction(OrderInterface $order)
    {
        $isCollectionPointSelected = (bool) $order->getCollectionPoint();
        $isPickupLocationSelected = (bool) $order->getPickupLocation();
        if ($isCollectionPointSelected || $isPickupLocationSelected) {
            // a delivery location was selected for quoting, rates already exist in database
            return self::ACTION_NONE;
        }

        $searchRequest = $order->getCollectionPointSearchRequest();
        $isSearchPending = $searchRequest && $searchRequest->isPending();
        if ($isSearchPending) {
            // collection point search not triggered yet, no rates to display
            return self::ACTION_NONE;
        }

        $isSearchPerformed = $searchRequest && $searchRequest->getPostcode() && $searchRequest->getCountryId();
        if ($isSearchPerformed) {
            // collection point search triggered, request applicable locations
            return self::ACTION_QUALIFY_COLLECTION_POINTS;
        }

        $searchRequest = $order->getPickupLocationSearchRequest();
        $isSearchPerformed = $searchRequest && $searchRequest->isActive();
        if ($isSearchPerformed) {
            // pickup location search triggered, request applicable locations
            return self::ACTION_QUALIFY_PICKUP;
        }

        // regular address quoting
        return self::ACTION_QUALIFY;
    }

    /**
     * Determine appropriate API action for the given order.
     *
     * Possible actions:
     * - Qualify
     * -- Get quotes for current cart
     * -- Get quotes for current cart, including possible collection points
     * -- Get quotes for current cart, including possible pickup locations
     * - Create
     * -- Persist placed order at the platform
     * -- Persist placed order at the platform and allocate shipments
     * - Update
     * -- Modify placed order that already exists at the platform
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getOrderAction(OrderInterface $order)
    {
        $platformOrderId = $order->getOrderId();
        $salesOrderId = $order->getSourceId();

        if (!$platformOrderId && !$salesOrderId) {
            // quoting, optionally with collection point search
            return $this->getQualifyOrderAction($order);
        }

        if (!$platformOrderId && $salesOrderId) {
            // manifest, optionally with shipment allocation
            return $this->getCreateOrderAction($order);
        }

        if ($platformOrderId && $salesOrderId) {
            // update placed order
            return $this->getUpdateOrderAction();
        }

        return '';
    }
}
